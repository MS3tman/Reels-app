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
            $table->string('target_url')->nullable();
            $table->unsignedBigInteger('target_views')->nullable()->default(0);
            $table->float('price')->nullable()->default(0);
            $table->string('offer_type')->nullable();
            $table->string('offer')->nullable();
            $table->string('video_manifest')->nullable();
            $table->boolean('status')->nullable()->default(false); // 
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
