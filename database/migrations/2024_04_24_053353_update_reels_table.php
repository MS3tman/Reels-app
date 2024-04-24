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
        //id-title-company_name-logo-target_url-btn_name
        Schema::table('reels', function (Blueprint $table) {
            $table->dropColumn(['price', 'target_views', 'offer_type', 'offer', 'status']);
            $table->string('company_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('btn_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
    }
};
