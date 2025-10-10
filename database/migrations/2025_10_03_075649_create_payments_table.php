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
        Schema::create('payments', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->unsignedBigInteger('id_pemesanan');
            $table->enum('metode_pembayaran', ['qris'])->default('qris'); // Simplified to single method
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['pending', 'berhasil', 'gagal'])->default('pending');
            $table->string('bukti_pembayaran')->nullable(); // For payment proof upload
            $table->text('payment_details')->nullable(); // JSON for additional payment info
            $table->datetime('payment_date')->nullable(); // When payment was made
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_pemesanan')->references('id_pemesanan')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
