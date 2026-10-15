<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * @group Autenticación
 */
class AuthController extends Controller
{
    /**
     * Registrar usuario
     *
     * @response 201 {"user":{"id":1,"name":"Ana","email":"ana@mail.com"},"token":"1|abc123..."}
     */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        return response()->json([
            'user'  => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ], 201);
    }

    /**
     * Iniciar sesión
     *
     * @response {"user":{"id":1,"name":"Ana","email":"ana@mail.com"},"token":"2|xyz789..."}
     * @response 401 {"message":"Credenciales inválidas"}
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return response()->json(['message' => 'Credenciales inválidas'], 401);
        }

        return response()->json([
            'user'  => $user,
            'token' => $user->createToken('api')->plainTextToken,
        ]);
    }

    /**
     * Cerrar sesión
     *
     * @authenticated
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->noContent();
    }
}