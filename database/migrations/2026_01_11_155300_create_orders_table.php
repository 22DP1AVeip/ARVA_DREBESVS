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
    Schema::create('orders', function (\Illuminate\Database\Schema\Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->cascadeOnDelete();

        $table->string('status')->default('processing'); // processing|shipped|delivered|cancelled
        $table->decimal('total', 10, 2)->default(0);

        // shipping/contact snapshot
        $table->string('full_name');
        $table->string('email');
        $table->string('phone')->nullable();
        $table->string('address');
        $table->string('city');
        $table->string('postcode');
        $table->string('country');

        $table->timestamps();
    });
}



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
