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
        Schema::dropIfExists('country_reel');
        Schema::create('country_reel', function (Blueprint $table) {
            $table->primary(['reel_id', 'country_id']);
            $table->foreignId('reel_id')->index();
            $table->foreignId('country_id')->index();

            $table->foreign('reel_id')
                  ->references('id')
                  ->on('reels')
                  ->onDelete('cascade');

            $table->foreign('country_id')
                ->references('id')
                ->on('countries')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
