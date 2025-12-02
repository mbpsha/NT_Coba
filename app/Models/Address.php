<?php
    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class Address extends Model
    {
        protected $primaryKey = 'id_alamat';

        protected $fillable = [
            'id_user',
            'label',
            'nama_penerima',
            'no_telp_penerima',
            'nama_jalan',
            'no_rumah',
            'alamat_lengkap',
            'kelurahan_desa',
            'kecamatan',
            'kabupaten',
            'provinsi',
            'kode_pos',
            'is_default'
        ];

        protected $casts = [
            'is_default' => 'boolean',
        ];

        public function user()
        {
            return $this->belongsTo(User::class, 'id_user', 'id_user');
        }
    }
