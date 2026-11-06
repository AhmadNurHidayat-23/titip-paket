@extends('layouts.app')
@section('title','Masuk petugas')
@section('content')
<section class="login-layout">
<div>
<span class="eyebrow">TITIK TERIMA, TITIK PERCAYA</span>
<h1>Paket aman.<br/>
<em>Tanggung jawab jelas.</em>
</h1>
<p>Catat paket yang datang, simpan bukti foto, dan pastikan pengambilan dilakukan dengan kode sekali pakai.</p>
<ol class="process">
<li>
<span>01</span>
<strong>Terima & catat</strong>
<small>Resi, penerima, foto, dan petugas.</small>
</li>
<li>
<span>02</span>
<strong>Sampaikan kode</strong>
<small>Kode acak melalui saluran privat.</small>
</li>
<li>
<span>03</span>
<strong>Verifikasi & serahkan</strong>
<small>Kode dan identitas diperiksa petugas.</small>
</li>
</ol>
</div>
<div class="login-panel">
<span class="eyebrow">RUANG PETUGAS</span>
<h2>Selamat datang kembali.</h2>
<p>Masuk menggunakan akun yang diberikan pengelola.</p>
<form method="post" action="/masuk">
@csrf
<label>Email<input name="email" type="email" required autocomplete="username" value="{{ old('email') }}">
</label>
<label>Kata sandi<input name="password" type="password" required autocomplete="current-password">
</label>
<button class="btn primary full" type="submit">Masuk ke pencatatan →</button>
</form>
<small>Tidak ada registrasi publik. Hubungi pengelola untuk pembuatan akun petugas.</small>
</div>
</section>
@endsection

