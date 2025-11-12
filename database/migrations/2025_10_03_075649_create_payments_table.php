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
            $table->id('id_payment');
            $table->unsignedBigInteger('id_order');
            $table->enum('metode_pembayaran', ['qris', 'transfer_bank', 'cod', 'ewallet', 'kartu_kredit'])->default('qris');
            $table->decimal('jumlah', 12, 2)->default(0);
            $table->enum('status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->string('bukti_transfer')->nullable(); // For payment proof upload
            $table->string('keterangan')->nullable(); // For transaction reference
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('id_order')->references('id_order')->on('orders')->onDelete('cascade');
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
