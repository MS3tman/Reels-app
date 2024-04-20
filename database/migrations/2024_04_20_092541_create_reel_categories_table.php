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
        Schema::create('reel_categories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('reel_id')->unsigned()->nullable(false);
            $table->string('category_title')->nullable(false);
            $table->timestamps();
            $table->foreign('reel_id')->references('id')->on('reels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reel_categories');
    }
};
