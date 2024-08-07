<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SupplierController extends Controller
{
    public function getSupplier()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }
        $suppliers = Supplier::where('bisnis_owner_id', $bo->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Success get supplier',
            'data' => $suppliers
        ], 200);
    }

    public function showSupplier($id)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }
        $supplier = Supplier::where('bisnis_owner_id', $bo->id)
            ->where('supplier_id', $id)
            ->first();
        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => 'Supplier not found'
            ], 404);
        }
        return response()->json([
            'status' => true,
            'message' => 'Success get supplier',
            'data' => $supplier
        ], 200);
    }

    public function storeSupplier(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_supplier' => 'required',
            'alamat' => 'required',
            'kabupaten' => 'required',
            'provinsi' => 'required',
            'desa' => 'required',
            'kecamatan' => 'required',
            'kode_pos' => 'required|numeric|min:5',
            'nomor_telepon' => 'required|numeric',
            'email' => 'required|email',
            'kontak_person' => 'required',
            'nomor_kontak_person' => 'required|numeric',
            'email_kontak_person' => 'required|email',
            'tipe_supplier' => 'required',
            'nomor_npwp' => 'required',
            'start_pks_date' => 'required|date',
            'end_pks_date' => 'required|date',
        ], [
            'nama_supplier.required' => 'Nama Supplier wajib diisi',
            'alamat.required' => 'Alamat wajib diisi',
            'kabupaten.required' => 'Kota wajib diisi',
            'provinsi.required' => 'Provinsi wajib diisi',
            'desa.required' => 'Kelurahan wajib diisi',
            'kecamatan.required' => 'Kecamatan wajib diisi',
            'kode_pos.required' => 'Kode Pos wajib diisi',
            'kode_pos.numerix' => 'Kode Pos harus berupa angka',
            'kode_pos.min' => 'Kode Pos harus minimal 5 karakter',
            'nomor_telepon.required' => 'Kontak Supplier wajib diisi',
            'nomor_telepon.numeric' => 'Kontak Supplier harus berupa angka',
            'email.required' => 'Email Supplier wajib diisi',
            'email.email' => 'Email Supplier harus valid',
            'kontak_person.required' => 'Nama PIC wajib diisi',
            'nomor_kontak_person.required' => 'Kontak PIC wajib diisi',
            'nomor_kontak_person.numeric' => 'Kontak PIC harus berupa angka',
            'email_kontak_person.required' => 'Email PIC wajib diisi',
            'email_kontak_person.email' => 'Email PIC harus valid',
            'tipe_supplier.required' => 'Tipe Supplier wajib diisi',
            'nomor_npwp.required' => 'Nomor NPWP wajib diisi',
            'start_pks_date.required' => 'Tanggal Kerjsama wajib diisi',
            'end_pks_date.required' => 'Tanggal Kerjsama wajib diisi',
            'start_pks_date.date' => 'Tanggal harus dalam format tanggal',
            'end_pks_date.date' => 'Tanggal harus dalam format tanggal',
        ]);
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }
        $bo = Auth::guard('bisnis_owner')->user();
        if (empty($bo)) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }
        $countSupplier = Supplier::count();
        $supplier = Supplier::create([
            'bisnis_owner_id' => $bo->id,
            'supplier_id' => 'SUPPID-' . date('Y') . date('m') . str_pad($countSupplier + 1, 5, "0", STR_PAD_LEFT) . '-' . rand(1000, 9999),
            'nama_supplier' => $request->nama_supplier,
            'alamat' => $request->alamat,
            'kabupaten' => $request->kabupaten,
            'provinsi' => $request->provinsi,
            'desa' => $request->desa,
            'kecamatan' => $request->kecamatan,
            'kode_pos' => $request->kode_pos,
            'nomor_telepon' => $request->nomor_telepon,
            'email' => $request->email,
            'website' => $request->website,
            'kontak_person' => $request->kontak_person,
            'nomor_kontak_person' => $request->nomor_kontak_person,
            'email_kontak_person' => $request->email_kontak_person,
            'tipe_supplier' => $request->tipe_supplier,
            'nomor_npwp' => $request->nomor_npwp,
            'start_pks_date' => $request->start_pks_date,
            'end_pks_date' => $request->end_pks_date,
            'catatan_tambahan' => $request->catatan_tambahan,
        ]);
        log_activity("Menambahkan Supplier $request->nama_supplier", 'Supplier', $bo->name, 1);
        return response()->json([
            'status' => true,
            'message' => 'Success create supplier',
            'data' => $supplier
        ], 200);
    }
    public function deleteSupplier($supplierId)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }
        $supplier = Supplier::where('bisnis_owner_id', $bo->id)
            ->where('supplier_id', $supplierId)
            ->first();
        if (!$supplier) {
            return response()->json([
                'status' => false,
                'message' => 'Supplier not found'
            ], 404);
        }
        $supplier->delete();
        return response()->json([
            'status' => true,
            'message' => 'Success delete supplier'
        ], 200);
    }
}
