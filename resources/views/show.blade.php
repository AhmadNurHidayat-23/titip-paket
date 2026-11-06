@extends('layouts.app')
@section('title','Paket '.$package->tracking)
@section('content')
<section class="workspace">
<a class="back-link" href="/">← Kembali ke daftar</a>
<div class="section-heading">
<div>
<span class="eyebrow">{{ $package->courier }} / CATATAN PAKET</span>
<h1 class="tracking-number">{{ $package->tracking }}</h1>
<p>{{ $package->recipient }} · {{ $package->unit??'Tanpa unit' }}</p>
</div>
<span class="badge {{ $package->status }}">{{ $package->status==='received'?'Masih dititipkan':'Sudah diambil' }}</span>
</div>
@if(session('pickup_code'))<div class="code-panel" role="status">
<div>
<span class="eyebrow">KODE PENGAMBILAN / TAMPIL SEKALI</span>
<output id="pickup-code">{{ session('pickup_code') }}</output>
<p>Sampaikan secara privat kepada penerima sebelum meninggalkan halaman. Kode tidak dapat dilihat kembali setelah halaman dimuat ulang.</p>
</div>
<button class="btn secondary" data-copy-target="pickup-code" type="button">Salin kode</button>
<span id="copy-status" role="status">
</span>
</div>
@endif
<div class="detail-layout">
<div>
<section class="panel photo-panel">
<span class="eyebrow">BUKTI PENERIMAAN / PRIVAT</span>
<img src="{{ route('package.show',$package) }}/foto" alt="Foto paket {{ $package->tracking }}">
<div class="photo-meta">
<small>Diterima {{ $package->created_at->format('d/m/Y H:i') }}</small>
<small>Petugas: {{ $package->creator->name }}</small>
</div>
</section>
<section class="timeline">
<h2>Jejak penanganan.</h2>
@foreach($package->events as $event)<article>
<p>{{ $event->description }}</p>
<small>{{ $event->actor->name }} · {{ $event->created_at->format('d/m/Y H:i') }}</small>
</article>
@endforeach
</section>
</div>
<aside class="panel collection-panel">
@if($package->status==='received')<span class="eyebrow">VERIFIKASI PENGAMBILAN</span>
<h2>Serahkan dengan yakin.</h2>
<p>Periksa kode yang dibawa penerima dan identitas pengambil sebelum mengonfirmasi.</p>
<form method="post" action="{{ route('package.show',$package) }}/ambil">
@csrf
<label>Kode pengambilan<input type="password" name="pickup_code" required maxlength="30" autocomplete="off">
</label>
<label>Nama pengambil<input name="collector" required maxlength="100" value="{{ old('collector') }}">
</label>
<label class="checkbox">
<input name="identity_checked" type="checkbox" value="1" required>Saya sudah memeriksa identitas dan kewenangan pengambil.</label>
<button class="btn primary full" type="submit">Verifikasi dan serahkan paket</button>
</form>
<small>Kode tidak dapat digunakan lagi setelah pengambilan berhasil.</small>
@else
<span class="eyebrow">SERAH TERIMA TERCATAT</span>
<h2>Paket sudah diambil.</h2>
<dl>
<div>
<dt>Pengambil</dt>
<dd>{{ $package->collected_by }}</dd>
</div>
<div>
<dt>Waktu</dt>
<dd>{{ $package->collected_at->format('d/m/Y H:i') }}</dd>
</div>
<div>
<dt>Kode pengambilan</dt>
<dd>Dinonaktifkan permanen</dd>
</div>
</dl>
<p>Riwayat penyerahan tetap disimpan untuk pemeriksaan aktivitas.</p>
@endif
</aside>
</div>
</section>
@endsection

