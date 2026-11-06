<?php

return [
    'accepted' => ':attribute harus dikonfirmasi.', 'regex' => 'Format :attribute tidak valid.',
    'required' => 'Kolom :attribute wajib diisi.', 'string' => ':attribute harus berupa teks.', 'integer' => ':attribute harus berupa bilangan bulat.',
    'email' => 'Masukkan alamat email yang valid.', 'uuid' => 'Kode :attribute tidak valid.', 'boolean' => ':attribute harus bernilai aktif atau nonaktif.',
    'array' => ':attribute harus berupa daftar.', 'distinct' => 'Menu yang sama hanya boleh muncul sekali.', 'in' => 'Pilihan :attribute tidak valid.',
    'unique' => ':attribute sudah digunakan.', 'image' => ':attribute harus berupa gambar.', 'mimes' => 'Format :attribute harus :values.',
    'date_format' => 'Format :attribute harus :format.', 'after_or_equal' => ':attribute harus sama dengan atau setelah :date.',
    'min' => ['numeric' => ':attribute minimal :min.', 'string' => ':attribute minimal :min karakter.', 'array' => ':attribute minimal :min item.'],
    'max' => ['numeric' => ':attribute maksimal :max.', 'string' => ':attribute maksimal :max karakter.', 'array' => ':attribute maksimal :max item.', 'file' => ':attribute maksimal :max kilobita.'],
    'attributes' => ['recipient' => 'nama penerima', 'tracking' => 'nomor resi', 'courier' => 'kurir', 'collector' => 'nama pengambil', 'pickup_code' => 'kode pengambilan', 'identity_checked' => 'pemeriksaan identitas', 'name' => 'nama', 'customer_name' => 'nama pemesan', 'notes' => 'catatan', 'price' => 'harga', 'stock' => 'stok', 'photo' => 'foto',
        'description' => 'deskripsi', 'category' => 'kategori', 'password' => 'kata sandi', 'items' => 'pesanan', 'checkout_key' => 'checkout', 'items.*.quantity' => 'jumlah porsi', 'from' => 'tanggal awal', 'to' => 'tanggal akhir'],
];
