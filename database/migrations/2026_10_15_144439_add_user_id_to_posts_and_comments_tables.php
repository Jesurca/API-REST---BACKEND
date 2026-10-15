<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('posts', fn(Blueprint $table) => $table->dropConstrainedForeignId('user_id'));
        Schema::table('comments', fn(Blueprint $table) => $table->dropConstrainedForeignId('user_id'));
    }
};
