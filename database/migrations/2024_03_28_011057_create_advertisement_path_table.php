<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('advertisement_path', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('advertisement_id')->unsigned()->nullable(false);
            $table->string('hls_format_path')->nullable(false);
            $table->string('manifest_file_name')->nullable(false);
            $table->timestamps();
            $table->foreign('advertisement_id')->references('id')->on('advertisements');
        });
    }

   
    public function down(): void
    {
        Schema::dropIfExists('advertisement_path');
    }
};
