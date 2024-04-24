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
        //campain_id-name-discount-locations-expire_date-ccount-price
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campain_id');
            $table->string('name');
            $table->unsignedInteger('discount')->nullable()->default(0);
            $table->string('locations', 500)->nullable();
            $table->date('expire_date')->nullable();
            $table->unsignedInteger('count')->nullable()->default(0);
            $table->unsignedFloat('price')->nullable()->default(0);
            $table->timestamps();
            $table->foreign('campain_id')->references('id')->on('campains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
