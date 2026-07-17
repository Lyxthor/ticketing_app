<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;
use Override;

class EventFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role == 'admin';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(Request $request): array
    {
        
        return [
            'judul'=>['required','string','max:255'],
            'deskripsi'=>['required', 'string'],
            'lokasi_id'=>['required', 'exists:lokasis,id'],
            'kategori_id'=>['required', 'exists:kategoris,id'],
            'tanggal_waktu'=>['required', 'date', 'after:now'],
            'gambar'=>['nullable','image','mimes:jpg,jpeg,png','max:2048'],

            'tikets'=>['required','array','min:1'],
            'tikets.*.tipe'=>['required','in:reguler,premium'],
            'tikets.*.harga'=>['required', 'numeric', 'min:0'],
            'tikets.*.stok'=>['required','integer','min:0']
        ];
    }
    #[Override]
    public function messages()
    {
        return [
            'judul.required' => 'Judul event wajib diisi.',
            'judul.string' => 'Judul harus berupa teks.',
            'judul.max' => 'Judul maksimal 255 karakter.',
            
            'deskripsi.required' => 'Deskripsi wajib diisi.',
            'deskripsi.string' => 'Deskripsi harus berupa teks.',
            
            'lokasi_id.required' => 'Lokasi wajib diisi.',
            'lokasi_id.exists' => 'Lokasi yang dipilih tidak valid.',
            
            
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            
            'tanggal_waktu.required' => 'Tanggal dan waktu wajib diisi.',
            'tanggal_waktu.date' => 'Format tanggal dan waktu tidak valid.',
            'tanggal_waktu.after' => 'Tanggal dan waktu harus lebih dari waktu sekarang.',
            
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Gambar harus bertipe: jpg, jpeg, atau png.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 2MB.',

            'tikets.required' => 'Wajib menyediakan minimal satu jenis tiket.',
            'tikets.array' => 'Format data tiket tidak valid.',
            'tikets.min' => 'Wajib menyediakan minimal satu jenis tiket.',

            'tikets.*.tipe.required' => 'Tipe tiket wajib diisi.',
            'tikets.*.tipe.in' => 'Tipe tiket harus berupa reguler atau premium.',
            
            'tikets.*.harga.required' => 'Harga tiket wajib diisi.',
            'tikets.*.harga.numeric' => 'Harga tiket harus berupa angka.',
            'tikets.*.harga.min' => 'Harga tiket tidak boleh minus.',
            
            'tikets.*.stok.required' => 'Stok tiket wajib diisi.',
            'tikets.*.stok.integer' => 'Stok tiket harus berupa bilangan bulat.',
            'tikets.*.stok.min' => 'Stok tiket tidak boleh minus.',
        ];
    }
    // protected function failedValidation(Validator $validator)
    // {
    //     // Dump the errors directly to your screen
    //     dd($validator->errors()->all());
    // }
}
