<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Peserta/pengunjung boleh tanpa email (akun instruktur dari Kantorku juga bisa tanpa email).
            $table->string('email')->nullable()->change();

            $table->string('username', 50)->nullable()->unique()->after('name');
            $table->string('role', 20)->default('peserta')->index()->after('password');
            $table->string('kantorku_id')->nullable()->unique()->after('role'); // id akun di SSO Kantorku
            $table->string('phone', 20)->nullable()->after('kantorku_id');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['username', 'role', 'kantorku_id', 'phone', 'is_active', 'last_login_at']);
        });
    }
};
