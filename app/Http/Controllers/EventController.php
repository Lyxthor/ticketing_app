<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Kategori;
use App\Models\Lokasi;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $order = $request->query('sort', 'asc');
        $kategori_id = $request->query('kategori_id');
        $events = \App\Models\Event::has('tikets');

        if ($kategori_id) {
            $events = $events->whereHas('kategori', function($query) use($kategori_id) {
                return $query->where('kategori_id', $kategori_id);
            });
        }
        else 
            $events = $events->has('kategori');
        $search = $request->query('search');
        if ($search) {
            $search = '%'.$search.'%';
            $events = $events->where('judul', 'LIKE', $search);
        }
        $events = $events->orderBy('tanggal_waktu', $order)
        ->paginate(2);
        $categories = Kategori::select(['id', 'nama'])->get();
        return view('pages.admin.events.index', compact('events', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Kategori::all('id', 'nama');
        $locations = Lokasi::select(['id', 'nama_lokasi'])->where('aktif', true)->get();
        return view('pages.admin.events.create', compact('categories', 'locations'));
    }
    public function store(EventFormRequest $request)
    {
        $img_url = 'konser.jpg';
        $validated_data = $request->validated();
        $event_data = Arr::except($validated_data, ["tikets"]);
        $tikets_data = Arr::only($validated_data, ["tikets"]);
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time()."_events.".$file->getClientOriginalExtension();
            $file->storeAs('events', $filename, 'public');
            $img_url = "events/".$filename;
        }
        try {
            $user_id = Auth::user()->id;
            DB::transaction(function() use($event_data, $tikets_data, $img_url, $user_id) {
                $event = Event::create([...$event_data, 'gambar'=>$img_url, 'user_id'=>$user_id]);
                $tikets_data = $tikets_data['tikets'];
                foreach($tikets_data as $data) {
                    $data["event_id"] = $event->id;
                    Tiket::create($data);
                }
            });
            return redirect()->route('admin.events.index')->with('success', 'Event berhasil dibuat.');
        }
        catch(Throwable $e) {
            
            return back()->with('error', 'Server error');
        }
    }
    public function edit(string $id) 
    {
        $event = Event::with(['kategori', 'tikets' => function($query) {
            $query->withCount('detailOrders as has_sales'); // <-- Tambahkan titik koma di sini
        }])->find($id);
        $categories = Kategori::select(['id', 'nama'])->get();
        $locations = Lokasi::select(['id', 'nama_lokasi'])->where('aktif', true)->get();

        $has_sales = $event->hasSales();
        if($has_sales)
            session()->flash('warning', 'Event ini sudah memiliki penjualan tiket. Beberapa field mungkin tidak dapat diubah.');
        // dd($event->tikets);
        if ($event) 
            return view('pages.admin.events.edit', compact('event', 'categories', 'locations', 'has_sales'));
        return back()->with('error', 'Event not found.');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(EventFormRequest $request, string $id)
    {
        $validated_data = $request->validated();
        $event = Event::find($id);

        if ($event->hasSales() && $event->tanggal_waktu->notEqualTo(Carbon::parse($validated_data['tanggal_waktu'])))
            return back()->with('error', 'Event memiliki penjualan.');

        $img_url = $event->gambar;
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $old_url = $event->gambar ?? 'konser.jpg';
            $filename = time()."_events.".$file->getClientOriginalExtension();
            if ($old_url != 'konser.jpg' && Storage::disk('public')->exists($old_url))
                Storage::disk('public')->delete($old_url);
            $img_url = "events/".$filename;
            $file->storeAs('events', $filename, 'public');
        } 
        try {
            DB::transaction(function() use($event, $validated_data, $request, $img_url) {
                $event_data = Arr::except($validated_data, ['tikets']);
                $event_data['gambar'] = $img_url;
                $event->update($event_data);

                $input_ids = collect($request->tikets)->pluck('id')->filter()->toArray();
                if (!$event->hasSales()) {
                    $to_delete_tikets = $event->tikets()->whereNotIn('id', $input_ids)->get();
                    foreach ($to_delete_tikets as $tiket) 
                        $tiket->delete();
                }
                $tikets_data = $request->tikets;
                foreach ($tikets_data as $index => $tiket) {
                    $tiket['id'] = $tiket['id'] ?? null;
                    $tiket['event_id'] = $event->id;
                    Tiket::updateOrCreate($tiket);
                }
                
                
                
            });
            return redirect()->route('admin.events.index')->with('Success', 'Updated');
        }
        catch(Throwable $e) {
            dd($e->getMessage());

            return back()->with('error', 'Server error.');
        }
    
       

        


    }

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with('kategori', 'tikets', 'lokasi')->find($id);
        if ($event) {
            $related_events = Event::related($event, 4)->get();
            return view('events.show', compact('event', 'related_events'));
        }
        return back()->with('error', 'Event not found.');
    }

   

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);
        if(!$event) 
            return back()->with('error', 'Event tidak ditemukan.');
        if ($event->hasSales()) 
            return back()->with('error', 'Event memiliki penjualan');

        $old_url = $event->gambar;
        if ($old_url != 'konser.jpg' && Storage::disk('public')->exists($old_url))
            Storage::disk('public')->delete($old_url);
        
        $event->delete();
        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dihapus.');
    }
}
