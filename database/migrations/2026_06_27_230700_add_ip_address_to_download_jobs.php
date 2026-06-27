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
        Schema::table('download_jobs', function (Blueprint $table) {
            $table->string('ip_address', 45)->nullable()->after('ip_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('download_jobs', function (Blueprint $table) {
             $table->dropColumn('ip_address');
        });
    }
};
