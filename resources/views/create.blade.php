@extends('layouts.app')
@section('title','Catat paket masuk')
@section('content')
<section class="workspace">
<a class="back-link" href="/">← Kembali ke daftar</a>
<div class="section-heading">
<div>
<span class="eyebrow">PENERIMAAN BARU</span>
<h1>Datang dengan resi.<br/>Tersimpan dengan bukti.</h1>
<p>Periksa label paket dan kondisi fisiknya sebelum mencatat.</p>
</div>
</div>
<form class="entry-layout" method="post" action="/paket" enctype="multipart/form-data">
@csrf
<div class="panel">
<h2>Identitas paket</h2>
<div class="form-grid">
<label>Nama penerima<input name="recipient" required maxlength="100" value="{{ old('recipient') }}">
</label>
<label>Unit / identifikasi penerima (opsional)<input name="unit" maxlength="60" value="{{ old('unit') }}">
</label>
<label>Nomor resi / identifikasi paket<input name="tracking" required minlength="5" maxlength="60" pattern="[A-Za-z0-9-]{5,60}" value="{{ old('tracking') }}" autocomplete="off">
</label>
<label>Kurir<select name="courier" required>
<option value="">Pilih kurir</option>
@foreach(\App\Models\Package::COURIERS as $c)<option @selected(old('courier')===$c)>{{ $c }}</option>
@endforeach
</select>
</label>
</div>
<label class="upload">Foto kondisi paket<input type="file" name="photo" required accept="image/jpeg,image/png,image/webp">
<small>JPG/PNG/WebP · Maksimal 4 MB, dimensi 4000 × 4000. Foto hanya dapat dibuka petugas.</small>
</label>
<button class="btn primary full" type="submit">Simpan dan buat kode pengambilan →</button>
</div>
<aside class="guidance">
<span class="eyebrow">SETELAH DICATAT</span>
<h2>Satu paket.<br/>Satu kode rahasia.</h2>
<ul>
<li>Kode acak hanya ditampilkan setelah penerimaan berhasil.</li>
<li>Sampaikan kode kepada penerima melalui saluran privat.</li>
<li>Kode asli tidak disimpan pada database; hanya hash-nya.</li>
<li>Pengambilan membutuhkan pemeriksaan identitas oleh petugas.</li>
</ul>
<p>Jangan membagikan foto label atau daftar penerima secara publik.</p>
</aside>
</form>
</section>
@endsection

