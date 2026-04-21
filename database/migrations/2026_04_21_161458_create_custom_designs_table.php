<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('garment_id')->nullable();
            $table->string('base_color')->default('#ffffff');
            $table->string('design_image_path')->nullable();
            $table->string('preset_design')->nullable();
            $table->string('design_position')->default('center');
            $table->string('design_size')->default('medium');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_designs');
    }
};