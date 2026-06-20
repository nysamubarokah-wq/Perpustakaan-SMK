<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('backgrounds', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('nama');
            $table->integer('harga')->default(0);
            $table->enum('type', ['color', 'image', 'video'])->default('color');
            $table->text('value'); // CSS gradient (color) atau path file (image/video)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backgrounds');
    }
};