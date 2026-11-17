<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Peserta: punya akun online (user_id) atau didaftarkan manual oleh petugas (user_id kosong).
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->unique()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->foreignId('participant_category_id')->nullable()->constrained('participant_categories')->nullOnDelete();
            $table->foreignId('blc_id')->nullable()->constrained()->nullOnDelete(); // BLC tempat mendaftar
            $table->timestamps();

            $table->index('name');
        });

        // Daftar kelas (jadwal diambil dari masing-masing BLC).
        Schema::create('training_classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('training_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('instructor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('name');
            $table->unsignedSmallInteger('quota')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('schedule_days')->nullable(); // contoh: "Senin, Rabu"
            $table->string('status', 15)->default('draft');
            $table->boolean('registration_open')->default(false);
            $table->timestamps();

            $table->index(['blc_id', 'status']);
        });

        // Pendaftaran peserta ke kelas (pilih salah satu sesi/kelas yang dibuka).
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->string('status', 15)->default('registered');
            $table->timestamp('registered_at')->nullable();
            $table->timestamps();

            $table->unique(['training_class_id', 'participant_id']);
        });

        // Daftar hadir: entry mengambil id kelas, laporan difilter per kelas.
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('training_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_id')->nullable()->constrained()->nullOnDelete();
            $table->date('attendance_date');
            $table->string('status', 10)->default('hadir');
            $table->string('note')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['training_class_id', 'participant_id', 'attendance_date'], 'attendances_unique_per_day');
            $table->index('attendance_date');
        });

        // Hasil pretest/posttest peserta.
        Schema::create('test_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_class_id')->nullable()->constrained()->nullOnDelete();
            $table->decimal('score', 5, 2)->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->timestamps();

            $table->index(['test_id', 'participant_id']);
        });

        Schema::create('test_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_attempt_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained()->nullOnDelete();
            $table->boolean('is_correct')->nullable();
            $table->timestamps();

            $table->unique(['test_attempt_id', 'question_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('test_answers');
        Schema::dropIfExists('test_attempts');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('enrollments');
        Schema::dropIfExists('training_classes');
        Schema::dropIfExists('participants');
    }
};
