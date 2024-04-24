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
        //reel_id-code_num-offer-target_views-price-expire_date-status
        Schema::create('campains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reel_id');
            $table->string('code_num')->nullable();
            $table->unsignedInteger('per_num')->nullable()->default(0);
            $table->unsignedInteger('target_views')->nullable()->default(0);
            $table->unsignedFloat('price')->nullable()->default(0);
            $table->date('expire_date')->nullable();
            $table->boolean('status')->nullable()->default(0);
            $table->timestamps();
            $table->foreign('reel_id')->references('id')->on('reels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campains');
    }
};
