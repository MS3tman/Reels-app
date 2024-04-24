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
        Schema::table('country_reel', function (Blueprint $table) {
            $table->dropForeign(['reel_id']);
            $table->foreign('reel_id')
                  ->references('id')
                  ->on('reels')->onDelete('cascade');

            $table->dropForeign(['country_id']);
            $table->foreign('country_id')
                ->references('id')
                ->on('countries')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('country_reel', function (Blueprint $table) {
            //
        });
    }
};
