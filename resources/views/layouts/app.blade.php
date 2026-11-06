<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>@yield('title','Titip Paket') · Titik Titip</title>@vite(['resources/css/app.css','resources/js/app.js'])</head>
<body>
<header>
<a class="brand" href="/">
<span class="brand-mark">↳</span>
<span>titik titip<span>PENGELOLAAN PAKET</span>
</span>
</a>
<nav>
@auth<a href="/">Daftar paket</a>
<a href="/laporan">Aktivitas petugas</a>
<span class="identity">{{ auth()->user()->name }}</span>
<form action="/keluar" method="post">
@csrf
<button class="text-btn" type="submit">Keluar ↗</button>
</form>
@else
<span>Area khusus petugas penerimaan</span>
@endauth
</nav>
</header>
@if(session('success'))<div class="notice success" role="status">{{ session('success') }}</div>
@endif @if($errors->any())<div class="notice error" role="alert">
@foreach($errors->all() as $error)<p>{{ $error }}</p>
@endforeach
</div>
@endif
<main>@yield('content')</main>
<footer>
<strong>titik titip.</strong>
<span>Setiap paket tercatat. Setiap pengambilan terverifikasi.</span>
<small>Akses petugas · Foto dan data penerima bersifat privat</small>
</footer>
</body>
</html>

