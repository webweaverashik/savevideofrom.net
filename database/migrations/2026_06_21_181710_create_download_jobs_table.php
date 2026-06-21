<?php

declare (strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_jobs', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            $table->text('url');
            $table->string('platform')->nullable()->index(); // detected slug
            $table->string('status')->default('pending')->index();
            $table->string('media_type')->nullable(); // video|audio|image

            // Metadata (from extract phase)
            $table->string('title')->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->unsignedInteger('duration')->nullable(); // seconds

                                                             // Requested output (from user selection)
            $table->string('requested_format')->nullable();  // mp4, mp3...
            $table->string('requested_quality')->nullable(); // 1080p, highest...
            $table->string('format_id')->nullable();         // yt-dlp format id

            // Result (from download phase)
            $table->string('file_name')->nullable();
            $table->string('file_path')->nullable();
            $table->unsignedBigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();

            // Errors
            $table->string('error_type')->nullable();
            $table->text('error_message')->nullable();

            // Abuse / analytics (no raw PII)
            $table->string('ip_hash', 64)->nullable()->index();
            $table->string('user_agent')->nullable();

            // Extra payload: full format list, extractor info, etc.
            $table->json('meta')->nullable();

            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_jobs');
    }
};
