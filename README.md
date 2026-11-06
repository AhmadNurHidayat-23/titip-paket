# Titip Paket

**Titik Titip** adalah aplikasi petugas untuk mencatat paket masuk, menyimpan foto privat, memverifikasi pengambilan dengan kode sekali pakai, dan menelusuri aktivitas penyerahan.

## Fitur

- Login petugas tanpa registrasi publik.
- Pencatatan penerima, unit, kurir, nomor resi unik, dan foto kondisi paket.
- Normalisasi nomor resi ke huruf kapital untuk mencegah duplikasi perbedaan huruf.
- Kode pengambilan acak ditampilkan setelah penerimaan; database hanya menyimpan hash SHA-256.
- Pencarian penerima/resi/unit, filter status, jumlah paket menunggu, dan aktivitas harian.
- Verifikasi kode dan pernyataan pemeriksaan identitas oleh petugas sebelum penyerahan.
- Kode dinonaktifkan dalam transaksi yang sama dengan pencatatan pengambilan; tidak dapat digunakan kembali.
- Nama pengambil, waktu, petugas, serta jejak penanganan tersimpan.
- Laporan aktivitas dengan filter tanggal/jenis dan ekspor CSV.
- Antarmuka Indonesia, responsif, validasi, pesan hasil, loading, CSRF, serta pembatasan login dan percobaan kode.

## Teknologi

Laravel 13, Blade, Tailwind CSS 4, Vite 7, SQLite, PHPUnit, dan Playwright. Memerlukan PHP 8.3+ dengan GD, Composer, serta Node.js 22.12+. Dependensi dikunci di lockfile.

## Instalasi dan menjalankan

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
New-Item -ItemType File database/database.sqlite -ErrorAction SilentlyContinue
php artisan migrate
npm ci
npm run build
php artisan staff:create
php artisan serve --host=127.0.0.1 --port=8051
```

Buka `http://127.0.0.1:8051`. Perintah `staff:create` meminta nama, email, dan kata sandi minimal 12 karakter. Tidak ada akun/password bawaan dan tidak ada registrasi publik. Seeder tidak membuat paket fiktif. Jalankan `npm run dev` untuk pengembangan frontend.

Database khusus proyek berada di `database/database.sqlite`; foto ada di `storage/app/private/packages` dan hanya dilayani melalui route terautentikasi. Jangan membuat symlink direktori privat. Keduanya diabaikan Git. Jika menggunakan MySQL, buat database khusus proyek dan sesuaikan `DB_*` sebelum migrasi.

## Alur petugas

1. Periksa label dan kondisi paket, lalu catat resi, penerima, kurir, unit jika ada, serta foto.
2. Setelah berhasil, salin kode yang ditampilkan dan sampaikan secara privat kepada penerima. Kode asli tidak dapat dilihat kembali setelah halaman dimuat ulang; pengelola perlu memastikan kode diteruskan saat penerimaan.
3. Saat pengambilan, cari paket, periksa identitas dan kewenangan pengambil, lalu masukkan kode serta nama pengambil.
4. Konfirmasi penyerahan. Status menjadi diambil, kode dihapus, dan petugas/waktu tercatat.

Pernyataan pemeriksaan identitas merupakan tanggung jawab petugas; aplikasi tidak mengklaim verifikasi dokumen otomatis. Jangan membagikan kode, foto label, atau ekspor penerima secara publik. Setelah kode diteruskan, tangani kode yang hilang melalui prosedur pengelola; aplikasi sengaja tidak menampilkan kembali kode asli.

## Pengujian

```powershell
php artisan route:list --except-vendor
php artisan test
npm run build
npm run test:e2e
```

PHPUnit memakai SQLite dalam memori. Playwright memakai Microsoft Edge terpasang, port 8052, akun sementara berkata sandi acak, serta `database/.browser-test.sqlite` yang dibuat ulang khusus pengujian. Database aplikasi tidak disentuh. Foto contoh di `tests/fixtures` adalah ilustrasi bertanda paket uji, bukan data penerima nyata. Screenshot/trace hanya tersimpan di `test-results/` yang diabaikan Git.

Tes mencakup keunikan dan hash kode, kode salah/paket berbeda/pemakaian ulang, identitas wajib diperiksa, resi ganda, foto privat, pencarian, CSV, dan alur penerimaan hingga pengambilan. Viewport desktop/tablet/ponsel diuji; belum merupakan pengujian perangkat fisik.

## Operasional

Gunakan HTTPS, `APP_DEBUG=false`, document root `public/`, akun petugas terpercaya, serta pencadangan database dan foto privat. Browser dapat menyalin kode pada konteks aman seperti localhost/HTTPS; bila clipboard tidak tersedia, salin manual dari tampilan. Maksimal 300 baris per berkas kode aplikasi. Tidak ada CSS/JavaScript inline pada Blade. Kredensial, database, screenshot, build, vendor, dan node_modules tidak disertakan di repository.
