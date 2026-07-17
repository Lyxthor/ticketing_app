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
        'tanggal_waktu' => 'datetime',
    ];
    public function lokasi() 
    {
        return $this->belongsTo(Lokasi::class, 'lokasi_id', 'id');
    }
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
    public function scopeRelated(Builder $query, Event $current_event, int $limit)
    {
        return $query->where('kategori_id', $current_event->kategori_id)
        ->where('id', '!=', $current_event->id)
        ->upcoming()
        ->take($limit);
    }
    public function getStatusAttribute()
{
    $hour_diff = $this->getHourDiff();
    // Jika waktu acara masih di masa depan (selisih > 0)
    if ($hour_diff > 0) {
        return "UPCOMING";
    }

    // Jika acara sedang berlangsung (sudah mulai, tapi belum lewat 3 jam)
    // Nilainya negatif/nol, misal: 0, -1, -2, -3
    if ($hour_diff >= -3) {
        return "ONGOING";
    }

    // Jika sudah lewat lebih dari 3 jam (misal: -4, -5)
    return "COMPLETED";
}

private function getHourDiff()
{
    $from = Carbon::parse($this->tanggal_waktu);
    $curr = Carbon::now('Asia/Jakarta');

    // Tambahkan parameter false agar menghasilkan angka signed (+/-)
    return $curr->diffInHours($from, false);
}
    public function hasSales() : bool
    {
        return $this->orders()->exists();
    }
    public function scopeUpcoming(Builder $query)
    {
        $curr = Carbon::now();
        return $query->where("tanggal_waktu", ">=", $curr->toString());
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
        $def_path = asset('storage/konser.jpg');
        $img_path = $this->gambar;
        
        $is_url = filter_var($img_path, FILTER_VALIDATE_URL);
        if ($is_url) 
            return $img_path;
        $is_exists = $img_path && Storage::disk('public')->exists($img_path);
        if ($is_exists) 
            return asset('storage/'.$img_path);
        return $def_path;
    }
    
}
