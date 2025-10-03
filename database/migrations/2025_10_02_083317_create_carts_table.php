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
        Schema::create('carts', function (Blueprint $table) {
            $table->id('id_keranjang');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');

            // Unique constraint: satu user hanya bisa punya satu cart aktif
            $table->unique('id_user');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
