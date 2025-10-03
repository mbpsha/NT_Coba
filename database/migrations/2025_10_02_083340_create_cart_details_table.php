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
        Schema::create('cart_details', function (Blueprint $table) {
            $table->id('id_detail_keranjang');
            $table->unsignedBigInteger('id_keranjang');
            $table->unsignedBigInteger('id_produk');
            $table->integer('jumlah')->default(1);
            $table->decimal('harga_satuan', 12, 2)->default(0);
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('id_keranjang')->references('id_keranjang')->on('carts')->onDelete('cascade');
            $table->foreign('id_produk')->references('id_produk')->on('products')->onDelete('cascade');

            // Unique constraint: satu produk hanya bisa ada satu kali di cart yang sama
            $table->unique(['id_keranjang', 'id_produk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_details');
    }
};
