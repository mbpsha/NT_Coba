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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id('id_alamat');
            $table->unsignedBigInteger('id_user');
            $table->string('label')->default('Rumah'); // Rumah, Kantor, dll
            $table->string('nama_penerima');
            $table->string('no_telp_penerima');
            $table->text('alamat_lengkap');
            $table->string('kota');
            $table->string('provinsi');
            $table->string('kode_pos', 10);
            $table->boolean('is_default')->default(false);
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
        Schema::dropIfExists('addresses');
    }
};
