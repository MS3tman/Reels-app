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
        Schema::table('category_reel', function (Blueprint $table) {
            $table->dropForeign(['reel_id']);
            $table->foreign('reel_id')
                  ->references('id')
                  ->on('reels')->onDelete('cascade');

            $table->dropForeign(['category_id']);
            $table->foreign('category_id')
                ->references('id')
                ->on('categories')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('category_reel', function (Blueprint $table) {
            //
        });
    }
};
