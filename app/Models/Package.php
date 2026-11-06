<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = ['id'];

    protected $hidden = ['pickup_hash', 'photo_path'];

    public const COURIERS = ['JNE', 'J&T', 'SiCepat', 'Pos Indonesia', 'TIKI', 'Lainnya'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected function casts(): array
    {
        return ['collected_at' => 'datetime'];
    }

    public function events()
    {
        return $this->hasMany(PackageEvent::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
