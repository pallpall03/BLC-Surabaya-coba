<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // "Area BLC": lokasi BLC yang ditampilkan di landing page.
        Schema::create('blcs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('region', 20)->nullable()->index(); // pusat, utara, selatan, timur, barat
            $table->text('address')->nullable();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('description')->nullable();
            $table->string('photo_path')->nullable();
            $table->boolean('show_on_landing')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Instruktur (dan akun pengunjung) yang terhubung ke BLC tertentu.
        // Hak akses instruktur tetap global; tabel ini hanya menandai BLC asal/penugasan.
        Schema::create('blc_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['blc_id', 'user_id']);
        });

        Schema::create('participant_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('visitor_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Sesi bersifat global (dipakai semua BLC).
        Schema::create('training_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->time('start_time');
            $table->time('end_time');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('training_sessions');
        Schema::dropIfExists('visitor_categories');
        Schema::dropIfExists('participant_categories');
        Schema::dropIfExists('blc_user');
        Schema::dropIfExists('blcs');
    }
};
