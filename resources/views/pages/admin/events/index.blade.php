@extends('layouts.admin')

@section('title', 'Manajemen Event')

@section('content')

    <div class="container mx-auto p-10">
        <div class="flex">
            <h1 class="text-3xl font-semibold mb-4">Manajemen Event</h1>
            <a 
            href="{{ route('admin.events.create') }}" 
            class="btn btn-primary ml-auto">
                Tambah Event
            </a>
        </div>
        
        <div class="w-full mb-4">
            <form method="GET" class="join justify-start w-full">
                <select name="kategori_id" class="select join-item w-1/12">
                    <option value="">
                        kategori
                    </option>
                    @forelse($categories as $index => $kategori)
                    <option value="{{ $kategori->id }}" @selected($kategori->id == request()->query('kategori_id'))>
                        {{ $kategori->nama }}
                    </option>
                    @empty
                    @endforelse
                </select>
                <select name="sort" class="select join-item w-1/12">
                    <option value="'">
                        order by
                    </option>
                    <option value="asc" @selected(request()->query('sort')=='asc')>
                        asc
                    </option>
                    <option value="desc" @selected(request()->query('sort')=='desc')>
                        desc
                    </option>
                </select>
                <input 
                type="text" 
                name="search"
                value="{{ request()->query('search') }}"
                class="input join-item w-3/12" 
                placeholder="cari judul dan lokasi..." />
                <button 
                type="submit" 
                class="btn btn-success join-item w-1/12 px-4">
                    Cari Event
                </button>
            </form>
        </div>
        <div class="overflow-x-auto rounded-box bg-white p-5 shadow-xs">
            <table class="table">
                <!-- head -->
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Thumbnail</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($events as $index => $event)
                    <tr>
                        <th>{{ $index + 1 }}</th>
                        <td>
                            <img  src="{{ $event->getImageUrlAttribute() }}" class="max-w-50" />
                        </td>
                        <td>{{ $event->judul }}</td>
                        <td>{{ $event->kategori_id }}</td>
                        <td>{{ $event->tanggal_waktu->format("d M Y, H:i") }}</td>
                        <td>{{ $event->lokasi }}</td>
                        <td>
                            @php
                                $status = $event->getStatusAttribute();
                                $badge_color = "";
                                switch($status) {
                                    case "ONGOING" :
                                        $badge_color = "success";
                                    break;
                                    case "UPCOMING" :
                                        $badge_color = "warning";
                                    break;
                                    case "COMPLETED" :
                                        $badge_color = "ghost";
                                    break;
                                }
                            @endphp
                            <span class="badge badge-soft badge-{{ $badge_color }}">
                                {{ $status }}
                            </span>
                        <td>
                            <a 
                            href="{{ route('events.show', ['id'=>$event->id]) }}"
                            class="btn btn-sm bg-primary text-white" 
                            data-id="{{ $event->id }}">
                                View
                            </a>
                            <a 
                            href="{{ route('admin.events.edit', ['id'=>$event->id]) }}"
                            class="btn btn-sm btn-warning mr-2" 
                            data-id="{{ $event->id }}" 
                            data-nama="{{ $event->nama }}">
                                Edit
                            </a>
                            <button 
                            class="btn btn-sm bg-red-500 text-white" 
                            onclick="openDeleteModal(this)" 
                            data-id="{{ $event->id }}">
                                Hapus
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="text-center">Tidak ada event tersedia.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {!! $events->appends(request()->except('page'))->links() !!}
        </div>
    </div>
    

    <!-- Add Category Modal -->
    <dialog id="add_modal" class="modal">
        <form method="POST" action="{{ route('admin.events.store') }}" class="modal-box">
            @csrf
            <h3 class="text-lg font-bold mb-4">Tambah Event</h3>
            <div class="form-control w-full mb-4">
                <label class="label mb-2">
                    <span class="label-text">Nama Event</span>
                </label>
                <input type="text" placeholder="Masukkan nama Event" class="input input-bordered w-full" name="nama" required />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button class="btn" onclick="add_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Edit Category Modal With Retrieve ID -->
     <dialog id="edit_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('PUT')

            <input type="hidden" name="category_id" id="edit_category_id">

            <h3 class="text-lg font-bold mb-4">Edit Event</h3>
            <div class="form-control w-full mb-4">
                <label class="label mb-2">
                    <span class="label-text">Nama Event</span>
                </label>
                <input type="text" placeholder="Masukkan nama Event" class="input input-bordered w-full" value="Event Contoh" id="edit_category_name" name="nama" />
            </div>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Simpan</button>
                <button class="btn" onclick="edit_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <!-- Delete Modal -->
    <dialog id="delete_modal" class="modal">
        <form method="POST" class="modal-box">
            @csrf
            @method('DELETE')

            <input type="hidden" name="category_id" id="delete_category_id">

            <h3 class="text-lg font-bold mb-4">Hapus Event</h3>
            <p>Apakah Anda yakin ingin menghapus Event ini?</p>
            <div class="modal-action">
                <button class="btn btn-primary" type="submit">Hapus</button>
                <button class="btn" onclick="delete_modal.close()" type="reset">Batal</button>
            </div>
        </form>
    </dialog>

    <script>
        function openEditModal(button) {
            const name = button.dataset.nama;
            const id = button.dataset.id;
            const form = document.querySelector('#edit_modal form');
            
            document.getElementById("edit_category_name").value = name;
            document.getElementById("edit_category_id").value = id;

             // Set action dengan parameter ID
            form.action = `{{ url('/admin/events') }}/${id}`

            edit_modal.showModal();
        }

        function openDeleteModal(button) {
            const id = button.dataset.id;
            const form = document.querySelector('#delete_modal form');
            document.getElementById("delete_category_id").value = id;

            // Set action dengan parameter ID
            form.action = `{{ url('/admin/events') }}/${id}`

            delete_modal.showModal();
        }
</script>


@endsection