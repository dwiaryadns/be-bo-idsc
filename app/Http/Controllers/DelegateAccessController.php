<?php

namespace App\Http\Controllers;

use App\Models\DelegateAccess;
use App\Models\HakAkses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class DelegateAccessController extends Controller
{
    public function getDelegateAccess()
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ]);
        }
        $delegate = DelegateAccess::with('hak_akses')->where('bisnis_owner_id', $bo->id)->get();
        return response()->json([
            'status' => true,
            'message' => 'Success get delegate access',
            'data' => $delegate
        ]);
    }

    public function storeDelegateAccess(Request $request)
    {
        $bo = Auth::guard('bisnis_owner')->user();
        if (!$bo) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ]);
        }
        $validator = Validator::make($request->all(), [
            'role' => 'required',
            'name' => 'required',
            'email' => 'required|email|max:255|unique:delegate_accesses|regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/',
            'password' => [
                'required',
                'string',
                'confirmed',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[!@#$%^&*(),.?":{}|<>_]/',
                'regex:/[0-9]/'
            ],
        ], [
            'password.regex' => 'Password must contain at least 1 Uppercase Word, 1 Special Character, and 1 Number',
            'email.regex' => 'Invalid format email'
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors, 'message' => 'Gagal Delegate'], 422);
        }

        try {
            $delegate =  DelegateAccess::create([
                'bisnis_owner_id' => $bo->id,
                'role' => $request->role,
                'name' => $request->name,
                'email' => $request->email,
                'password' =>  Hash::make($request->password)
            ]);

            $hakAkses = new HakAkses();
            $hakAkses->delegate_access_id = (int)$delegate->id;
            $permissions = [];
            foreach ($request->permission as $permission) {
                $permissions[] = $permission;
            }
            $hakAkses->permission = json_encode($permissions);
            $hakAkses->save();

            Log::info("data : " . $delegate);
            log_activity("Memberikan Akses $request->role kepada $request->email", 'Hak Akses', Auth::guard('bisnis_owner')->user()->name, 1);
            return response()->json([
                'status' => true,
                'message' => 'Success add delegate access',
                'data' => $delegate
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'errors' => 'Terjadi kesalahan saat menyimpan data',
                'message' => 'Gagal Delegate',
                'log' => $th->getLine() . $th->getMessage()
            ]);
        }
    }

    public function destroy($id)
    {
        try {
            $delegateAccess = DelegateAccess::find($id);
            $delegateAccess->delete();

            return response()->json(['status' => true, 'message' => 'Berhasil Menghapus Delegate'], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Gagal Menghapus Delegate', 'error' => $e->getMessage()], 500);
        }
    }
}
