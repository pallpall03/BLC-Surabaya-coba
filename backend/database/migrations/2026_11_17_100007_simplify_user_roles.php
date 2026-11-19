<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Role disederhanakan jadi 4: admin, koordinator, instruktur, user.
 * Akun lama: superadmin -> admin, peserta dan pengunjung -> user.
 */
return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')->where('role', 'superadmin')->update(['role' => 'admin']);
        DB::table('users')->whereIn('role', ['peserta', 'pengunjung'])->update(['role' => 'user']);

        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('user')->change();
        });
    }

    public function down(): void
    {
        // Hanya mengembalikan default kolom; role akun yang sudah digabung tidak bisa dipulihkan.
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 20)->default('peserta')->change();
        });
    }
};
