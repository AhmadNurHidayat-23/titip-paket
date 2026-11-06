<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Services\PackageService;
use App\Services\PhotoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class PackageController extends Controller
{
    public function index(Request $r)
    {
        $filters = $r->validate(['q' => 'nullable|string|max:80', 'status' => 'nullable|in:received,collected']);
        $q = Package::query()->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))->when($filters['q'] ?? null, fn ($q, $v) => $q->where(fn ($q) => $q->where('tracking', 'like', '%'.$v.'%')->orWhere('recipient', 'like', '%'.$v.'%')->orWhere('unit', 'like', '%'.$v.'%')));
        $packages = $q->latest()->paginate(15)->withQueryString();
        $summary = ['waiting' => Package::where('status', 'received')->count(), 'receivedToday' => Package::whereDate('created_at', today())->count(), 'collectedToday' => Package::whereDate('collected_at', today())->count()];

        return view('dashboard', compact('packages', 'filters', 'summary'));
    }

    public function create()
    {
        return view('create');
    }

    public function store(Request $r, PhotoService $photos, PackageService $s)
    {
        $r->merge(['tracking' => strtoupper(trim((string) $r->input('tracking')))]);
        $d = $r->validate(['recipient' => 'required|string|max:100', 'unit' => 'nullable|string|max:60', 'courier' => ['required', Rule::in(Package::COURIERS)], 'tracking' => ['required', 'string', 'max:60', 'regex:/^[A-Za-z0-9-]{5,60}$/', 'unique:packages'], 'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096|dimensions:max_width=4000,max_height=4000']);
        $path = $photos->store($r->file('photo'));
        unset($d['photo']);
        try {
            [$package,$code] = $s->receive($r->user(), $d, $path);
        } catch (\Throwable $e) {
            Storage::disk('local')->delete($path);
            throw $e;
        }

        return to_route('package.show', $package)->with('success', 'Paket diterima. Sampaikan kode kepada penerima melalui saluran privat.')->with('pickup_code', $code);
    }

    public function show(Package $package)
    {
        return view('show', ['package' => $package->load(['creator:id,name', 'events.actor:id,name'])]);
    }

    public function photo(Package $package)
    {
        abort_unless(Storage::disk('local')->exists($package->photo_path), 404);

        return Storage::disk('local')->response($package->photo_path, null, ['Cache-Control' => 'private, no-store']);
    }

    public function collect(Request $r, Package $package, PackageService $s)
    {
        $d = $r->validate(['pickup_code' => 'required|string|max:30', 'collector' => 'required|string|max:100', 'identity_checked' => 'required|accepted']);
        $s->collect($r->user(), $package, $d['pickup_code'], $d['collector']);

        return back()->with('success', 'Pengambilan terverifikasi. Kode telah dinonaktifkan permanen.');
    }
}
