<?php

namespace App\Http\Controllers\Core;

use App\Models\AccessFasyankes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AccessFasyankesController extends Controller
{

    public function __construct()
    {
        $this->middleware('check.token');
    }

    public function checkAccessFasyankes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }
        $user = AccessFasyankes::with('fasyankes', 'fasyankes.warehouse', 'fasyankes.bisnis_owner')
            ->where('username', $request->username)
            ->first();
        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'username not found'
            ], 404);
        }
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid username or password'
            ], 401);
        }

        if ($user->is_active == 0) {
            return response()->json([
                'status' => false,
                'message' => 'User not active'
            ], 403);
        }
        $fasyankes = $user->fasyankes;
        $data = [
            'username' => $user->username,
            'role' => $user->role,
            'id_profile' => $user->id_profile,
            'is_active' => '1',
            'created_by' => $user->created_by,
            'created_at' => $user->created_at,
            'wfid' => $user->wfid($fasyankes->fasyankesId),
            'fasyankes' => [
                'bisnis_owner' => $fasyankes->bisnis_owner,
                'warehouse' => $fasyankes->warehouse,
                'fasyankesId' => $fasyankes->fasyankesId,
                'type' => $fasyankes->type,
                "name" => $fasyankes->name,
                "address" => $fasyankes->address,
                "pic" => $fasyankes->pic,
                "pic_number" => $fasyankes->pic_number,
                "email" => $fasyankes->email,
                "is_active" => $fasyankes->is_active,
            ]
        ];

        return response()->json([
            'status' => true,
            'message' => 'Login Successfully',
            'data' => $data,
        ], 200);
    }

    public function storeAccessFasyankes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fasyankes_id' => 'required',
            'role' => 'required',
            'id_profile' => 'required',
            'username' => 'required|unique:access_fasyankes,username',
            'password' => [
                'required', 'string', 'confirmed', 'min:8', 'regex:/[A-Z]/', 'regex:/[!@#$%^&*(),.?":{}|<>_]/', 'regex:/[0-9]/'
            ],
            'created_by' => 'required',
        ], [
            'password.regex' => 'Password must contain at least 1 Uppercase Word, 1 Special Character, and 1 Number',
        ]);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }

        $checkFasyankesId = AccessFasyankes::where('fasyankes_id', $request->fasyankes_id)->first();
        if (!$checkFasyankesId) {
            return response()->json([
                'status' => false,
                'message' => 'Fasyankes not found'
            ], 404);
        }

        $user = AccessFasyankes::create([
            'fasyankes_id' => $request->fasyankes_id,
            'id_profile' => $request->id_profile,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'is_active' => 1,
            'created_by' => $request->created_by,
            'role' => $request->role,
        ]);

        $data = [
            'fasyankes_id' => $user->fasyankes_id,
            'id_profile' => $user->id_profile,
            'username' => $user->username,
            'is_active' => $user->is_active,
            'created_by' => $user->created_by,
            'role' => $user->role,
            'created_at' => $user->created_at,
        ];
        return response()->json([
            'status' => true,
            'message' => 'Access Fasyankes added successfully',
            'data' => $data,
        ], 200);
    }
}
