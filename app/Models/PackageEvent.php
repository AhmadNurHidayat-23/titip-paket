<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageEvent extends Model
{
    protected $guarded = ['id'];

    public function actor()
    {
        return $this->belongsTo(User::class, 'actor_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class);
    }
}
