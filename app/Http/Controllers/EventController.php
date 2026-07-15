<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventFormRequest;
use App\Models\Event;
use App\Models\Kategori;
use App\Models\Tiket;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
            $events->whereHas('kategori_id', function($query) use($kategori_id) {
                return $query->where('kategori_id', $kategori_id);
            });
        }
        else 
            $events->has('kategori');

        $search = $request->query('search');
        if ($search) {
            $events->where('judul', $search)
            ->orWhere('lokasi', $search);
        }
        $events->orderBy('tanggal_waktu', $order)
        ->paginate(10);
        return view('pages.admin.events.index', compact('$events'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $kategoris = Kategori::all('id', 'name');
        return view('pages.admin.events.create', compact('kategoris'));
    }
    public function store(EventFormRequest $request)
    {
        $img_url = 'konser.jpg';
        $validated_data = $request->validated();
        $event_data = Arr::except($validated_data, ["tikets"]);
        $tikets_data = Arr::only($validated_data, ["tikets"]);
        if ($request->hasFile('gambar')) {
            $file = $request->file('gambar');
            $filename = time()."_events".$file->getExtension();
            $file->storeAs('events', $filename, 'public');
        }
        try {
            DB::transaction(function() use($event_data, $tikets_data, $img_url) {
                $event = Event::create([...$event_data, 'gambar'=>$img_url]);
                foreach($tikets_data as $data) {
                    $data["event_id"] = $event->id;
                    Tiket::create($data);
                }
            });
            return redirect()->route('admin.event.index')->with('success', 'Event berhasil dibuat.');
        }
        catch(Throwable $e) {
            return back()->with('error', 'Server error');
        }
    }
    public function edit(string $id) 
    {
        $event = Event::with('kategori', 'tikets')->find($id);
        if ($event) 
            return view('pages.admin.events.edit', compact('event'));
        return back()->with('error', 'Event not found.');
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(EventFormRequest $request, string $id)
    {
        $validated_data = $request->validated();
        $event = Event::find($id);

        if ($event->hasSales() && $event->tanggal_waktu)
            return back()->with('error', 'Event memiliki penjualan.');

        if ($request->hasFile('gambar')) {
            $old_url = $event->gambar;
            $file = $request->file('gambar');
            $filename = time()."_events".$file->getExtension();

            if ($old_url != 'konser.jpg' && Storage::disk('public')->exists($old_url))
                Storage::disk('public')->delete($old_url);
            $file->storeAs('events', $filename, 'public');
        } 
        try {
            DB::transaction(function() use($event, $request) {
                $input_ids = $request->tikets->pluck('id')->filter()->toArray();
                $tikets_data = $request->tikets->toArray();
                foreach ($tikets_data as $tiket) {
                    $tiket['id'] = $tiket['id'] ?? null;
                    $tiket['event_id'] = $event->id;
                }
                Tiket::upsert($tikets_data, ['id'], ['stok', 'harga', 'tipe']);

                if (!$event->hasSales()) {
                    $to_delete_tikets = $event->tikets()->whereNotIn('id', $input_ids);
                    foreach ($to_delete_tikets as $tiket) 
                        $tiket->delete();
                }
                return redirect()->route('admin.event.index')->with('Success', 'Updated');
            });
        }
        catch(Throwable $e) {
            return back()->with('error', 'Server error.');
        }
    
       

        


    }

    

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::with('kategori', 'tikets')->find($id);
        if ($event) 
            return view('pages.admin.events.show', compact('event'));
        return back()->with('error', 'Event not found.');
    }

   

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $event = Event::find($id);
        if ($event->hasSales()) 
            return back()->with('error', 'Event tidak ditemukan.');

        $old_url = $event->gambar;
        if ($old_url != 'konser.jpg' && Storage::disk('public')->exists($old_url))
            Storage::disk('public')->delete($old_url);
        
        $event->delete();
        return redirect()->route('admin.event.index')->with('success', 'Event berhasil dihapus.');
    }
}
