@extends('layouts.app')
@section('title','Laporan aktivitas petugas')
@section('content')
<section class="workspace">
<div class="section-heading">
<div>
<span class="eyebrow">RIWAYAT / AKTIVITAS PETUGAS</span>
<h1>Setiap tindakan ada jejaknya.</h1>
<p>Penerimaan dan pengambilan berdasarkan waktu kejadian.</p>
</div>
<a class="btn secondary" href="/laporan?{{ http_build_query(array_merge($filters,['export'=>'csv'])) }}">Ekspor CSV ↓</a>
</div>
<form class="filters" method="get" action="/laporan">
<label>Dari tanggal<input type="date" name="from" required value="{{ $filters['from'] }}">
</label>
<label>Sampai tanggal<input type="date" name="to" required min="{{ $filters['from'] }}" value="{{ $filters['to'] }}">
</label>
<label>Aktivitas<select name="action">
<option value="">Semua aktivitas</option>
<option value="received" @selected(($filters['action']??'')==='received')>Penerimaan</option>
<option value="collected" @selected(($filters['action']??'')==='collected')>Pengambilan</option>
</select>
</label>
<button class="btn primary" type="submit">Terapkan</button>
</form>
<div class="report-stats">
<div>
<span>Paket diterima</span>
<strong>{{ $summary['received'] }}</strong>
</div>
<div>
<span>Paket diserahkan</span>
<strong>{{ $summary['collected'] }}</strong>
</div>
</div>
<div class="table-scroll">
<table>
<thead>
<tr>
<th>Waktu</th>
<th>Petugas</th>
<th>Aktivitas</th>
<th>Resi</th>
<th>Penerima</th>
<th>Keterangan</th>
</tr>
</thead>
<tbody>
@foreach($events as $e)<tr>
<td>{{ $e->created_at->format('d/m/Y H:i') }}</td>
<td>{{ $e->actor->name }}</td>
<td>{{ $e->action==='received'?'Diterima':'Diambil' }}</td>
<td>
<a class="mono" href="{{ route('package.show',$e->package) }}">{{ $e->package->tracking }}</a>
</td>
<td>{{ $e->package->recipient }}</td>
<td class="wrap">{{ $e->description }}</td>
</tr>
@endforeach
</tbody>
</table>
@if($events->isEmpty())<div class="empty">
<h3>Belum ada aktivitas pada periode ini.</h3>
<p>Ubah rentang tanggal untuk melihat catatan lain.</p>
</div>
@endif
</div>{{ $events->links() }}</section>
@endsection

