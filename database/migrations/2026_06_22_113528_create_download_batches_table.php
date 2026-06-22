<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('download_batches', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->text('source_url');
            $table->string('platform')->nullable();
            $table->string('title')->nullable();
            $table->string('status')->default('processing')->index();
            $table->unsignedInteger('total_items')->default(0);
            $table->string('requested_quality')->nullable();
            $table->string('requested_format')->nullable();
            $table->string('media_type')->nullable();
            $table->string('bus_batch_id')->nullable()->index();
            $table->string('zip_path')->nullable();
            $table->unsignedBigInteger('zip_size')->nullable();
            $table->text('error_message')->nullable();
            $table->string('ip_hash', 64)->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('download_batches');
    }
};