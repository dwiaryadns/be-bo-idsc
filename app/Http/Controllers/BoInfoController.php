<?php

namespace App\Http\Controllers;

use App\Models\BisnisOwner;
use App\Models\BoInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class BoInfoController extends Controller
{
    public function getBoInfo()
    {
        $user = Auth::guard('bisnis_owner')->user();

        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }

        try {
            $bo_info = BoInfo::with('bisnis_owner', 'bisnis_owner.legal_doc_bo')->where('bisnis_owner_id', $user->id)
                ->first();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Berhasil Get Data',
            'data' => $bo_info,
        ]);
    }


    public function storeBoInfo(Request $request)
    {
        Log::info('storeBoIfo called', $request->all());

        $user = Auth::guard('bisnis_owner')->user();
        if (empty($user)) {
            Log::error('Pengguna tidak terautentikasi.');
            return response()->json([
                'status' => false,
                'message' => 'Pengguna tidak terautentikasi.'
            ], 401);
        }

        // Kondisi apakah sedang melakukan update atau create
        $id = $request->id;

        $rules = [
            'businessEmail' => [
                'required',
                'email',
                Rule::unique('bo_infos')->ignore($id),  // Abaikan email yang sedang diupdate dengan ID yang sama
            ],
            'businessType' => 'required',
            'businessName' => 'required',
            'phone' => 'required|numeric',
            'mobile' => 'required|numeric',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'postal_code' => 'required|numeric|regex:/^\d{5,}$/',
        ];

        $messages = [
            'businessType.required' => 'Tipe Bisnis wajib diisi.',
            'businessName.required' => 'Nama Bisnis wajib diisi.',
            'phone.required' => 'Nomor Telepon wajib diisi.',
            'phone.numeric' => 'Nomor Telepon harus berupa angka.',
            'mobile.required' => 'Nomor Ponsel wajib diisi.',
            'mobile.numeric' => 'Nomor Ponsel harus berupa angka.',
            'address.required' => 'Alamat wajib diisi.',
            'province.required' => 'Provinsi wajib diisi.',
            'city.required' => 'Kota wajib diisi.',
            'subdistrict.required' => 'Kecamatan wajib diisi.',
            'village.required' => 'Kelurahan/Desa wajib diisi.',
            'postal_code.required' => 'Kode Pos wajib diisi.',
            'postal_code.numeric' => 'Kode Pos harus berupa angka.',
            'postal_code.regex' => 'Kode Pos minimal 5 digit.',
            'businessEmail.required' => 'Email Bisnis wajib diisi.',
            'businessEmail.email' => 'Format Email Bisnis tidak valid.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'message' => 'Gagal', 'errors' => $errors], 422);
        }

        Log::info('User authenticated', ['user_id' => $user->id, 'request_data' => $request->all()]);

        $y = date('Y');
        $m = date('m');
        $countBo = BisnisOwner::whereNotNull('email_verified_at')->count();

        BoInfo::updateOrCreate(
            ['id' => $id], // Jika ada id, maka update, jika tidak, create
            [
                'bisnis_owner_id' => $user->id,
                'businessId' => $$request->businessId ??  $y . $m . '000' . $countBo, // Hanya generate businessId jika create
                'businessEmail' => $request->businessEmail,
                'businessType' => $request->businessType,
                'businessName' => $request->businessName,
                'phone' => $request->phone,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'province' => $request->province,
                'city' => $request->city,
                'subdistrict' => $request->subdistrict,
                'village' => $request->village,
                'postal_code' => $request->postal_code,
                'status' => 'apply'
            ]
        );

        log_activity('Upload Bisnis Owner Info', 'Bisnis Owner Info', $user->name, 1);

        return response()->json([
            'status' => true,
            'message' => 'Berhasil',
        ]);
    }
}
