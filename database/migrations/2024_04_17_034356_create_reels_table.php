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
        Schema::create('reels', function (Blueprint $table) {
            //id-user_id-title-target_url-target_views-price-offer_type-offer-status-video_manifest
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title');
            $table->string('company_name')->nullable();
            $table->string('logo')->nullable();
            $table->string('btn_name')->nullable();
            $table->string('target_url')->nullable();
            $table->string('video_manifest')->nullable();
            $table->timestamps();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reels');
    }
};
