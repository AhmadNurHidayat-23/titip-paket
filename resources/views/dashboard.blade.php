@extends('layouts.app')
@section('title','Paket masuk & pengambilan')
@section('content')
<section class="workspace">
<div class="section-heading">
<div>
<span class="eyebrow">RUANG OPERASIONAL / {{ now()->format('d.m.Y') }}</span>
<h1>Semua paket, jelas posisinya.</h1>
<p>Daftar penerimaan dan pengambilan yang tercatat oleh petugas.</p>
</div>
<a class="btn primary" href="/paket/baru">+ Catat paket masuk</a>
</div>
<div class="stats">
<div class="featured">
<span>Masih dititipkan</span>
<strong>{{ $summary['waiting'] }}</strong>
<small>Menunggu verifikasi pengambilan</small>
</div>
<div>
<span>Masuk hari ini</span>
<strong>{{ $summary['receivedToday'] }}</strong>
<small>Resi yang berhasil dicatat</small>
</div>
<div>
<span>Diambil hari ini</span>
<strong>{{ $summary['collectedToday'] }}</strong>
<small>Kode sudah dinonaktifkan</small>
</div>
</div>
<form class="filters" action="/" method="get">
<label>Cari paket<input name="q" maxlength="80" value="{{ $filters['q']??'' }}" placeholder="Nama penerima, nomor resi, atau unit">
</label>
<label>Status<select name="status">
<option value="">Semua status</option>
<option value="received" @selected(($filters['status']??'')==='received')>Masih dititipkan</option>
<option value="collected" @selected(($filters['status']??'')==='collected')>Sudah diambil</option>
</select>
</label>
<button class="btn secondary" type="submit">Terapkan filter</button>
</form>
<div class="table-scroll">
<table>
<thead>
<tr>
<th>Penerima / unit</th>
<th>Resi / kurir</th>
<th>Masuk</th>
<th>Status</th>
<th>Aksi</th>
</tr>
</thead>
<tbody>
@foreach($packages as $p)<tr>
<td>
<strong>{{ $p->recipient }}</strong>
<small>{{ $p->unit??'Tanpa unit' }}</small>
</td>
<td>
<span class="mono">{{ $p->tracking }}</span>
<small>{{ $p->courier }}</small>
</td>
<td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
<td>
<span class="badge {{ $p->status }}">{{ $p->status==='received'?'Dititipkan':'Diambil' }}</span>
</td>
<td>
<a class="btn small secondary" href="{{ route('package.show',$p) }}">Lihat paket ↗</a>
</td>
</tr>
@endforeach
</tbody>
</table>
@if($packages->isEmpty())<div class="empty">
<span>↳</span>
<h3>Belum ada paket pada pencarian ini.</h3>
<p>Catat paket pertama atau gunakan kata pencarian lain.</p>
<a class="btn secondary" href="/paket/baru">Catat paket masuk</a>
</div>
@endif
</div>{{ $packages->links() }}</section>
@endsection

