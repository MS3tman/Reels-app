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
        Schema::create('reels_copoun', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reel_id')->unsigned()->nullable(false);
            $table->string('copoun_name')->nullable(false);
            $table->string('discount')->nullable(false);
            $table->json('location')->nullable(false);
            $table->string('expiry_date')->nullable(false);
            $table->integer('target_copouns')->nullable(false);
            $table->float('copoun_price')->nullable();
            $table->float('total_price')->nullable();
            $table->timestamps();
            $table->foreign('reel_id')->references('id')->on('reels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reels_copoun');
    }
};
