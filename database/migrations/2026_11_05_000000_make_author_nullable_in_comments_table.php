<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Hace la columna 'author' nullable en la tabla comments.
     * La API V1 no utiliza este campo (usa user_id en su lugar).
     */
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('author')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            $table->string('author')->nullable(false)->change();
        });
    }
};
