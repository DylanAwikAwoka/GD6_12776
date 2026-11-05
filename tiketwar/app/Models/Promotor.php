<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Promotor extends Model
{
    protected $fillable = ['nama', 'kontak']; // BARU

    public function konsers(): HasMany // BARU: satu promotor, banyak konser
    {
        return $this->hasMany(Konser::class);
    }
}