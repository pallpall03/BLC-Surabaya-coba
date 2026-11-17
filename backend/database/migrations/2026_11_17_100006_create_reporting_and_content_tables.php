<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Buku tamu pengunjung. BLC diambil dari akun pengunjung yang login, bukan dipilih manual.
        Schema::create('guestbook_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('visitor_category_id')->nullable()->constrained('visitor_categories')->nullOnDelete();
            $table->string('group_name')->nullable();
            $table->unsignedSmallInteger('visitor_count')->default(1);
            $table->string('purpose')->nullable();
            $table->timestamp('visited_at');
            $table->timestamps();

            $table->index(['blc_id', 'visited_at']);
        });

        // Laporan kegiatan per kelurahan/kecamatan, termasuk kegiatan di luar BLC.
        Schema::create('activity_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blc_id')->constrained()->cascadeOnDelete();
            $table->foreignId('training_class_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('training_session_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity_type', 15)->default('di_blc'); // di_blc | di_luar_blc
            $table->string('title');
            $table->date('activity_date');
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans')->nullOnDelete();
            $table->foreignId('kelurahan_id')->nullable()->constrained('kelurahans')->nullOnDelete();
            $table->string('location_detail')->nullable();
            $table->unsignedInteger('participant_count')->default(0);
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->index(['blc_id', 'activity_date']);
            $table->index('activity_date');
        });

        // Artikel untuk menu artikel di landing page.
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body');
            $table->string('cover_path')->nullable();
            $table->string('status', 10)->default('draft')->index();
            $table->timestamp('published_at')->nullable()->index();
            $table->timestamps();
        });

        // Pengaturan landing page yang diatur admin (key-value).
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->string('group', 30)->default('landing')->index();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
        Schema::dropIfExists('articles');
        Schema::dropIfExists('activity_reports');
        Schema::dropIfExists('guestbook_entries');
    }
};
