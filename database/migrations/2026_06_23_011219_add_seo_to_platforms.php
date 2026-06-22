<?php

declare (strict_types = 1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->string('page_slug')->nullable()->unique()->after('slug');
            $table->boolean('is_published')->default(false)->after('is_featured');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('h1')->nullable();
            $table->text('intro')->nullable();
            $table->json('howto')->nullable();
            $table->json('faqs')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('platforms', function (Blueprint $table) {
            $table->dropColumn(['page_slug', 'is_published', 'meta_title', 'meta_description', 'h1', 'intro', 'howto', 'faqs']);
        });
    }
};
