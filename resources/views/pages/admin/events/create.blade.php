@extends('layouts.admin')
@section('title', 'Tambah Event')
@section('content')
    <div class="card container mx-auto w-8/12 bg-base-100 shadow-sm">
        <section class="flex justify-between items-center px-4 pt-6 pb-4 w-full bg-stone-100">
            <h1 class="text-lg font-bold text-slate-700">
                Create Event Form
            </h1>
            <a 
            href="{{ route('admin.events.index') }}"
            class="link text-blue-400 text-sm">
                <- Back to index
            </a>
        </section>
        <form 
        method="POST" 
        action="{{ route('admin.events.store') }}" 
        enctype="multipart/form-data"
        class="flex justify-end flex-wrap px-4 pt-4 pb-6 w-full">
            @csrf
            <section 
            x-data="{
                image_url: null,
                previewFile(event) {
                    const file = event.target.files[0];
                    if(!file) return;

                    const reader = new FileReader();
                    reader.onload = e=>{
                        this.image_url = e.target.result;
                    }
                    reader.readAsDataURL(file)
                },
                clearImage() {
                    this.image_url = null;
                }
            }"
            class="w-1/2" 
            id="event-image-field">
                <h2 class="text-lg text-stone-600 mb-4">Pilih Gambar</h2>
                <template x-if="image_url">
                    <div class="img-container mb-4">
                        <img :src="image_url" class="mb-2 w-3/4 aspect-square object-cover rounded-md" />
                        <button 
                        type="button" 
                        class="btn"
                        @click="clearImage()">
                            Clear image
                        </button>
                    </div>
                    
                </template>
                <input 
                type="file" 
                name="gambar"
                accept="image/*"
                class="file-input" 
                @change="previewFile(event)" />
                <p class="mt-2 text-xs text-slate-500"><span class="text-red-500">*</span> Format yang diterima : JPG, PNG dan JPEG</p>
            </section>
            <section id="event-data-field" class="flex flex-col gap-4 mb-6 w-1/2">
                <h2 class="text-lg text-stone-600">Data Event</h2>
                <div class="space-y-2">
                    <label class="block">
                        <span class="text-sm font-medium">Judul</span>
                        <span class="text-error">*</span>
                    </label>
                    <input 
                    name="judul" 
                    value="{{ old('judul') }}"
                    class="input input-bordered w-full 
                    @error('judul')
                        border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500
                    @enderror">
                    @error('judul')
                        <p class="mt-2 text-sm text-red-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block">
                        <span class="text-sm font-medium">Kategori</span>
                        <span class="text-error">*</span>
                    </label>
                    <select 
                    value="{{ old('kategori_id') }}" 
                    name="kategori_id" class="select select-bordered w-full">
                        @forelse($categories as $index => $kategori)
                        <option value="{{ $kategori->id }}">
                            {{ $kategori->nama }}
                        </option>
                        @empty
                        @endforelse
                    </select>
                </div>
                <div class="space-y-2">
                    <label class="block">
                        <span class="text-sm font-medium">Lokasi</span>
                        <span class="text-error">*</span>
                    </label>
                    <input 
                    name="lokasi" 
                    value="{{ old('lokasi') }}"
                    class="input input-bordered w-full
                    @error('lokasi')
                    border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500
                    @enderror">
                    @error('lokasi')
                        <p class="mt-2 text-sm text-red-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block">
                        <span class="text-sm font-medium">Tanggal & Waktu</span>
                        <span class="text-error">*</span>
                    </label>
                    <input 
                    type="datetime-local" 
                    name="tanggal_waktu" 
                    value="{{ old('tanggal_waktu') }}"
                    class="input input-bordered w-full 
                    @error('tanggal_waktu')
                    border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500
                    @enderror">
                    @error('tanggal_waktu')
                        <p class="mt-2 text-sm text-red-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <div class="space-y-2">
                    <label class="block">
                        <span class="text-sm font-medium">Deskripsi</span>
                        <span class="text-error">*</span>
                    </label>
                    <textarea name="deskripsi" class="textarea textarea-bordered w-full 
                    @error('deskripsi')
                    border-red-300 text-red-900 placeholder-red-300 focus:border-red-500 focus:ring-red-500
                    @enderror">{{ old('deskripsi') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-2 text-sm text-red-600 font-semibold flex items-center">
                            <svg class="w-4 h-4 mr-1 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>
                <button
                type="submit"
                class="btn btn-primary">
                    Submit
                </button>
            </section>
            <section id="tickets-data-fields" class="w-1/2">
                <div 
                class="flex justify-between items-center w-full mb-4" 
                x-data="{triggerTicketAppend() {$dispatch('append_ticket')}}">
                    <h2 class="text-lg text-stone-600">Data Tiket</h2>
                    <button
                    type="button"
                    class="link block text-sm text-blue-500"
                    @click.prevent="triggerTicketAppend()">
                        + Tambah Tiket
                    </button>
                </div>
                <div 
                x-data="{
                    opened_id: 0,
                    base_data: {
                        stok: 0,
                        harga: 0,
                        tipe: 'premium'
                    },
                    tickets: [],
                    prepend(index) {
                        if(this.tickets.length <= 1) return;
                        index = index - 1;
                        this.tickets.splice(index, 1)
                    },
                    toggleOpen(el, index) {
                        if(this.tickets.length <= 1) {
                            el.parentElement.open = true;
                        } else {
                            el.parentElement.open = !el.parentElement.open
                        }
                        event.preventDefault()
                        event.stopPropagation()
                    },
                    append() {
                        this.tickets.push({...this.base_data})
                    },
                    init() {
                        this.tickets = @js(old('tikets')) ?? [{...this.base_data}]
                    },
                    
                }"
                @append_ticket.window="append()"
                class="join join-vertical w-full" 
                id="tickets-container">
                    <template x-for="(ticket, index) in tickets">
                        <details class="collapse join-item border border-base-300" name="my-accordion" name="tickets-accordion" :open="index == 0">
                            <summary 
                            class="collapse-title flex justify-between items-center px-4 text-base font-medium"
                            @click.prevent.stop="toggleOpen($el, index)">
                                <span class="font-sbold" x-text="'Tiket #'+(index+1)"></span>
                                <button 
                                type="button"
                                class="btn btn-sm bg-red-500 text-white" 
                                :disabled="tickets.length <= 1"
                                :class="tickets.length <= 1 ? 'bg-gray-100' : 'bg-red-500'"
                                @click="prepend(index)">
                                    Hapus
                                </button>
                            </summary>
                            <div class="collapse-content">
                                <div class="join">
                                    <div class="join-item border-0 w-4/12">
                                        <label class="block">
                                            <span class="text-sm font-medium">Harga</span>
                                            <span class="text-error">*</span>
                                        </label>
                                        <input 
                                        type="number" 
                                        :name="'tikets['+index+'][harga]'" 
                                        :value="ticket.harga" 
                                        class="input input-bordered w-full rounded-s-none">
                                    </div>
                                    <div class="join-item border-0 w-4/12">
                                        <label class="block">
                                            <span class="text-sm font-medium">Stok</span>
                                            <span class="text-error">*</span>
                                        </label>
                                        <input 
                                        type="number" 
                                        :name="'tikets['+index+'][stok]'" 
                                        :value="ticket.stok" 
                                        class="input input-bordered w-full rounded-none border-x-none">
                                    </div>
                                    <div class="join-item border-0 w-4/12">
                                        <label class="block">
                                            <span class="text-sm font-medium">Tipe</span>
                                            <span class="text-error">*</span>
                                        </label>
                                        <select 
                                        :name="'tikets['+index+'][tipe]'"
                                        :value="ticket.tipe"
                                        class="select select-bordered w-full rounded-s-none" >
                                            <option value="reguler">
                                                Reguler
                                            </option>
                                            <option value="premium">
                                                Premium
                                            </option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </details>
                    </template>
                </div>
            </section>
        </form>
    </div>
    <script>
        window.addEventListener('append_ticket', (e) => {
            console.log('Event caught on window! Detail data is:', e.detail);
        });
        // const template = document.getElementById('ticket-accordion-template').innerHTML;
        // const container = document.getElementById('tickets-container');

        // const base_data = {
        //     harga: 0,
        //     stok: 0,
        //     tipe: 'premium'
        // }
        // const tickets = [base_data];
        // function render() {
        //     container.innerHTML = ""
        //     tickets.forEach((ticket, index) => {
        //         console.log(index)
        //         const html = template.replace("INDEX_PLACEHOLDER", (index+1))
        //         .replace("HARGA_PLACEHOLDER", ticket.harga)
        //         .replace("STOK_PLACEHOLDER", ticket.stok)
        //         .replace("TIPE_PLACEHOLDER", ticket.tipe);
        //         container.insertAdjacentHTML('beforeend', html);
        //     })
        // }
        // function append(e) {
        //     e.preventDefault()
        //     tickets.push(base_data)
        //     render()
        // }
        // render()
    </script>
@endsection