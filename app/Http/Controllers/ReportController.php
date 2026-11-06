<?php

namespace App\Http\Controllers;

use App\Models\PackageEvent;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $r)
    {
        $f = $r->validate(['from' => 'nullable|date_format:Y-m-d', 'to' => 'nullable|date_format:Y-m-d|after_or_equal:from', 'action' => 'nullable|in:received,collected', 'export' => 'nullable|in:csv']);
        $from = $f['from'] ?? now()->startOfMonth()->toDateString();
        $to = $f['to'] ?? today()->toDateString();
        $query = PackageEvent::with(['actor:id,name', 'package'])->whereDate('created_at', '>=', $from)->whereDate('created_at', '<=', $to)->when($f['action'] ?? null, fn ($q, $a) => $q->where('action', $a));
        if ($r->input('export') === 'csv') {
            return response()->streamDownload(function () use ($query) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Waktu', 'Petugas', 'Aktivitas', 'Resi', 'Penerima', 'Keterangan'], ',', '"', '');
                foreach ($query->orderBy('id')->cursor() as $e) {
                    fputcsv($out, [$e->created_at->toIso8601String(), $this->safe($e->actor->name), $e->action, $this->safe($e->package->tracking), $this->safe($e->package->recipient), $this->safe($e->description)], ',', '"', '');
                }fclose($out);
            }, 'aktivitas-paket.csv', ['Content-Type' => 'text/csv']);
        }$summary = ['received' => (clone $query)->where('action', 'received')->count(), 'collected' => (clone $query)->where('action', 'collected')->count()];
        $events = $query->latest()->paginate(25)->withQueryString();
        $filters = [...$f, 'from' => $from, 'to' => $to];

        return view('reports', compact('events', 'summary', 'filters'));
    }

    private function safe(string $s): string
    {
        return preg_match('/^[\s=+@-]/u', $s) ? "'".$s : $s;
    }
}
