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
        Schema::create('orders', function (Blueprint $table) {
            $table->id('id_pemesanan');
            $table->unsignedBigInteger('id_user');
            $table->enum('status', ['pending', 'dikonfirmasi', 'diproses', 'dikirim', 'selesai', 'dibatalkan'])->default('pending');
            $table->decimal('total_harga', 12, 2)->default(0);
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_user')->references('id_user')->on('users')->onDelete('cascade');
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
