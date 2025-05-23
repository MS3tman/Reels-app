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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code');
            $table->string('phone_number')->unique();
            $table->date('bdate')->nullable();
            $table->char('gender', 1)->nullable();
            $table->string('password');
            $table->string('image_path')->nullable();
            $table->string('address')->nullable();
            $table->string('vtoken')->nullable();
            $table->boolean('active')->enum(true, false)->default(true)->nullable(false);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
