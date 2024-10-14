<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\KategoriBarangApotek;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;

class BarangImport implements ToModel, WithValidation, SkipsOnError
{
    use Importable, SkipsErrors;
    private $data = [];
    private $importErrors = [];
    private $rowNumber = 0;

    public function startRow(): int
    {
        return 2;
    }
    public function model(array $row)
    {
        // Cek apakah baris kosong
        if (empty(array_filter($row))) {
            return null; // Jika baris kosong, lewati
        }

        $this->rowNumber++;
        try {
            $bo = Auth::guard('bisnis_owner')->user();

            $supplier = Supplier::where('nama_supplier', $row[7])->where('bisnis_owner_id', $bo->id)->first();
            $kategori = KategoriBarangApotek::where('nama', $row[1])->first();

            if (!$supplier) {
                throw new \Exception("Supplier tidak ditemukan.");
            }

            if (!$kategori) {
                throw new \Exception("Kategori tidak ditemukan.");
            }

            $barang = new Barang([
                'barang_id' => 'BID-' . date('Y') . date('m') . str_pad(Barang::count() + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
                'nama_barang' => $row[0],
                'kategori_id' => $kategori->kategori_id,
                'satuan' => $row[2],
                'harga_beli' => $row[3],
                'harga_jual' => $row[4],
                'deskripsi' => $row[5],
                'expired_at' => date('Y-m-d', strtotime(str_replace("'", "", $row[6]))),
                'supplier_id' => $supplier->supplier_id,
            ]);
            $this->data[] = $barang;

            return $barang;
        } catch (\Throwable $e) {
            $this->importErrors[] = [
                'row' => $this->rowNumber + 1, // +1 karena startRow() dimulai dari 2
                'data' => implode(', ', $row),
                'error' => $e->getMessage()
            ];
            return null;
        }
    }


    public function rules(): array
    {
        return [
            '0' => 'required|unique:barangs,nama_barang',
            '1' => 'required',
            '2' => 'required',
            '3' => 'required',
            '4' => 'required',
            '5' => 'required',
            '6' => 'required',
            '7' => 'required',
        ];
    }

    public function customValidationMessages()
    {
        return [
            '0.required' => 'Nama Barang harus diisi.',
            '0.unique' => 'Nama Barang sudah tersedia.',
            '1.required' => 'Kategori harus diisi.',
            '2.required' => 'Satuan harus diisi.',
            '3.required' => 'Harga Beli harus diisi.',
            '4.required' => 'Harga Jual harus diisi.',
            '5.required' => 'Tanggal Kadaluarsa harus diisi.',
            '6.required' => 'Tanggal Kadaluarsa harus diisi.',
            '7.required' => 'Supplier harus diisi.',
        ];
    }

    public function getData()
    {
        return $this->data;
    }

    public function getImportErrors()
    {
        return $this->importErrors;
    }

    public function formatErrors()
    {
        return collect($this->importErrors)->map(function ($error) {
            return "Baris " . ($error['row'] - 1) . "  " . ": {$error['error']} (Data: {$error['data']})";
        })->toArray();
    }
}
