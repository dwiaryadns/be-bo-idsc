<?php

namespace App\Imports;

use App\Models\Barang;
use App\Models\KategoriBarangApotek;
use App\Models\Supplier;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;

class BarangImport implements ToModel
{
    private $data = [];

    public function model(array $row)
    {
        $bo = Auth::guard('bisnis_owner')->user();

        $supplier = Supplier::where('nama_supplier', $row[7])->where('bisnis_owner_id', $bo->id)->first();
        $kategori = KategoriBarangApotek::where('nama', $row[1])->first();

        if (!$supplier) {
            return null;
        }

        $barang =  new Barang([
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
    }
    public function getData()
    {
        return $this->data;
    }
}
