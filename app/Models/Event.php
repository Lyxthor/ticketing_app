<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

class Event extends Model
{
     use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'tanggal' => 'datetime',
    ];

    public function tikets()
    {
        return $this->hasMany(Tiket::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
    public function getStatusAttribute()
    {
        $hour_diff = $this->getHourDiff();
        if($hour_diff < 0) return "UPCOMING";
        if($hour_diff <= 3) return "ONGOING";
        return "COMPLETED";
    }
    public function hasSales() : bool
    {
        return $this->has("orders");
    }
    public function scopeUpcoming(Builder $query)
    {
        $curr = Carbon::now();
        return $query->where("tanggal_waktu", "<", $curr->toString());
    }
    public function scopeOngoing(Builder $query)
    {
        $from = Carbon::now();
        $next = $from->addHours(3);
        return $query->where("tanggal_waktu", ">=", $from->toString())
        ->where("tanggal_waktu", "<=", $next->toString());
    }
    public function scopeCompleted(Builder $query)
    {
        $from = Carbon::now();
        $next = $from->addHours(3);
        return $query->where("tanggal_waktu", ">", $next->toString());
    }
    public function getImageUrlAttribute()
    {
        $def_path = 'konser.jpg';
        $img_path = $this->gambar;

        $is_url = filter_var($img_path, FILTER_VALIDATE_URL);
        if ($is_url == false) return $def_path;

        $is_exists = Storage::exists($img_path);
        if ($is_exists == false) return $def_path;

        return $img_path;
    }
    private function getHourDiff()
    {
        $from = Carbon::parse($this->tanggal_waktu);
        $curr = Carbon::now();

        $hour_diff = $curr->diffInHours($from);
        return $hour_diff;
    }
}
