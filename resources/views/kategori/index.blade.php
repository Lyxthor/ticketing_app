@extends('admin_layouts')

@section('title', 'Manajemen Kategori')

@section('content')

@if (session('success'))
    <div class="toast toast-bottom toast-center">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>

    <script>
    setTimeout(() => {
        document.querySelector('.toast')?.remove()
    }, 3000)
    </script>
@endif

<div class="p-6">
    <div class="flex mb-4">
        <h1 class="text-2xl font-semibold">Manajemen Kategori</h1>
        <button class="btn btn-primary ml-auto" onclick="add_modal.showModal()">
            Tambah
        </button>
    </div>

    <div class="overflow-x-auto bg-white p-4 rounded shadow">
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($kategori as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->nama }}</td>
                    <td>
                        <button class="btn btn-sm btn-info"
                                onclick="openEditModal({{ $item->id }}, '{{ $item->nama }}')">
                            Edit
                        </button>

                        <button class="btn btn-sm btn-error"
                                onclick="openDeleteModal({{ $item->id }})">
                            Hapus
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ADD --}}
<dialog id="add_modal" class="modal">
    <form method="POST" action="{{ route('kategori.store') }}" class="modal-box">
        @csrf
        <h3 class="font-bold mb-4">Tambah Kategori</h3>
        <input type="text" name="nama" class="input input-bordered w-full" required>
        <div class="modal-action">
            <button class="btn btn-primary">Simpan</button>
            <button type="button" class="btn" onclick="add_modal.close()">Batal</button>
        </div>
    </form>
</dialog>

{{-- EDIT --}}
<dialog id="edit_modal" class="modal">
    <form method="POST" class="modal-box" id="editForm">
        @csrf
        @method('PUT')
        <h3 class="font-bold mb-4">Edit Kategori</h3>
        <input type="text" name="nama" id="edit_nama"
               class="input input-bordered w-full" required>
        <div class="modal-action">
            <button class="btn btn-primary">Update</button>
            <button type="button" class="btn" onclick="edit_modal.close()">Batal</button>
        </div>
    </form>
</dialog>

{{-- DELETE --}}
<dialog id="delete_modal" class="modal">
    <form method="POST" class="modal-box" id="deleteForm">
        @csrf
        @method('DELETE')
        <h3 class="font-bold">Hapus Kategori</h3>
        <p class="py-4">Yakin ingin menghapus?</p>
        <div class="modal-action">
            <button class="btn btn-error">Hapus</button>
            <button type="button" class="btn" onclick="delete_modal.close()">Batal</button>
        </div>
    </form>
</dialog>

<script>
function openEditModal(id, nama) {
    document.getElementById('edit_nama').value = nama
    document.getElementById('editForm').action = `/kategori/${id}`
    edit_modal.showModal()
}

function openDeleteModal(id) {
    document.getElementById('deleteForm').action = `/kategori/${id}`
    delete_modal.showModal()
}
</script>

@endsection
