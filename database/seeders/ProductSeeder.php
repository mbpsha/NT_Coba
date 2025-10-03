<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // IoT Stech Smart Garden tanpa panel surya
        Product::create([
            'nama' => 'IoT Stech Smart Garden (belum include panel Surya) + Buku Panduan Perakitan + Source Code',
            'deskripsi' => 'Stech Smart Garden adalah sistem irigasi otomatis berbasis IoT skala kecil yang dirancang untuk membantu petani dan masyarakat yang hobi berkebun dalam mengelola kebutuhan air secara efisien. Sistem ini memanfaatkan sensor kelembaban tanah untuk memantau kondisi media tanam secara real-time, lalu mengaktifkan pompa air sesuai kebutuhan. Dengan integrasi dashboard, pengguna dapat memantau data kelembaban dan status pompa kapan saja. Paket ini menggunakan adaptor listrik sebagai sumber tenaga utama, cocok untuk penggunaan indoor atau area yang sudah tersedia sumber listrik PLN. Include: Perangkat IoT lengkap, sensor kelembaban tanah, pompa air mini, buku panduan perakitan, dan source code program.',
            'harga' => 750000,
            'foto' => null, // Bisa diisi nanti dengan path gambar
            'stok' => 25,
        ]);

        // IoT Stech Smart Garden dengan panel surya
        Product::create([
            'nama' => 'IoT Stech Smart Garden (include panel Surya) + Buku Panduan Perakitan + Source Code',
            'deskripsi' => 'Stech Smart Garden adalah sistem irigasi otomatis berbasis IoT skala kecil yang dirancang untuk membantu petani dan masyarakat yang hobi berkebun dalam mengelola kebutuhan air secara efisien. Sistem ini memanfaatkan sensor kelembaban tanah untuk memantau kondisi media tanam secara real-time, lalu mengaktifkan pompa air sesuai kebutuhan. Dengan integrasi dashboard, pengguna dapat memantau data kelembaban dan status pompa kapan saja. Varian ini dilengkapi dengan panel surya yang membuat sistem lebih ramah lingkungan dan dapat beroperasi di area outdoor tanpa ketergantungan listrik PLN. Perfect untuk greenhouse, kebun outdoor, atau area remote. Include: Perangkat IoT lengkap, sensor kelembaban tanah, pompa air mini, panel surya + baterai, buku panduan perakitan, dan source code program.',
            'harga' => 1250000,
            'foto' => null, // Bisa diisi nanti dengan path gambar
            'stok' => 15,
        ]);
    }
}
