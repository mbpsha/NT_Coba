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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id('id_review');
            $table->unsignedBigInteger('id_produk');
            $table->unsignedBigInteger('id_user');
            $table->integer('rating')->default(1);
            $table->text('komentar')->nullable();
            $table->string('dokumentasi')->nullable(); // foto atau video review
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_produk')->references('id_produk')->on('products')->onDelete('cascade');
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
