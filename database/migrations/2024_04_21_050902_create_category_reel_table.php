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
        Schema::create('category_reel', function (Blueprint $table) {
            $table->primary(['reel_id', 'category_id']);
            $table->foreignId('reel_id')->index();
            $table->foreignId('category_id')->index();

            $table->foreign('reel_id')
                  ->references('id')
                  ->on('reels')
                  ->onDelete('cascade');

            $table->foreign('category_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('category_reel');
    }
};
