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
            $table->string('full_name')->nullable(false);
            $table->string('email')->unique()->nullable(false);
            $table->timestamp('email_verified_at')->nullable();
            $table->string('country_code')->nullable(false);
            $table->string('phone_number')->unique()->nullable(false);
            $table->string('password')->nullable(false);
            $table->string('image_path')->nullable();
            $table->string('address')->nullable();
            $table->string('type')->enum('user', 'admin')->default('user')->nullable(false);
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
