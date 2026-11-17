<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Materi dari/untuk instruktur.
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Pertemuan per materi (total 8 sampai 10; batas dijaga di validasi aplikasi).
        Schema::create('meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('meeting_number');
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['material_id', 'meeting_number']);
        });

        // Modul PDF: hanya boleh diakses peserta (disimpan di disk privat).
        Schema::create('module_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('meeting_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('file_path');
            $table->string('original_name');
            $table->string('mime_type', 100)->default('application/pdf');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // Setiap materi punya satu pretest dan satu posttest.
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->string('type', 10); // pretest | posttest
            $table->string('title');
            $table->unsignedSmallInteger('duration_minutes')->nullable();
            $table->unsignedTinyInteger('passing_score')->nullable();
            $table->timestamps();

            $table->unique(['material_id', 'type']);
        });

        // Soal dibuat sendiri oleh instruktur (standar 5 soal, tidak dikunci di database).
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('test_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->text('body');
            $table->timestamps();

            $table->unique(['test_id', 'position']);
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('label', 2); // A, B, C, D, ...
            $table->text('body');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();

            $table->unique(['question_id', 'label']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('tests');
        Schema::dropIfExists('module_files');
        Schema::dropIfExists('meetings');
        Schema::dropIfExists('materials');
    }
};
