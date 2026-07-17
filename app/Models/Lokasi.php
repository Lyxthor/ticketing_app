<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lokasi extends Model
{
    use SoftDeletes;
    protected $guarded = ['id'];

    public function events() 
    {
        return $this->hasMany(Event::class, 'lokasi_id', 'id');
    }
}
