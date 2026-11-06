<?php

namespace App\Services;

use App\Models\Package;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PackageService
{
    public function receive(User $actor, array $data, string $photo): array
    {
        return DB::transaction(function () use ($actor, $data, $photo) {
            do {
                $code = 'TP-'.strtoupper(Str::random(10));
                $hash = hash('sha256', $code);
            } while (Package::where('pickup_hash', $hash)->exists());
            $p = Package::create($data + ['slug' => (string) Str::uuid(), 'photo_path' => $photo, 'pickup_hash' => $hash, 'created_by' => $actor->id]);
            $p->events()->create(['actor_id' => $actor->id, 'action' => 'received', 'description' => 'Paket diterima dan kode pengambilan diterbitkan.']);

            return [$p, $code];
        });
    }

    public function collect(User $actor, Package $package, string $code, string $collector): void
    {
        DB::transaction(function () use ($actor, $package, $code, $collector) {
            $p = Package::lockForUpdate()->findOrFail($package->id);
            if ($p->status !== 'received' || ! $p->pickup_hash || ! hash_equals($p->pickup_hash, hash('sha256', strtoupper(trim($code))))) {
                throw ValidationException::withMessages(['pickup_code' => 'Kode tidak sesuai atau sudah digunakan.']);
            }$p->update(['status' => 'collected', 'pickup_hash' => null, 'collected_by' => $collector, 'collected_at' => now()]);
            $p->events()->create(['actor_id' => $actor->id, 'action' => 'collected', 'description' => 'Paket diserahkan kepada '.$collector.' setelah kode dan identitas diperiksa.']);
        }, 3);
    }
}
