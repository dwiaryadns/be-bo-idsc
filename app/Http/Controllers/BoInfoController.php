<?php

namespace App\Http\Controllers;

use App\Models\BoInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BoInfoController extends Controller
{
    public function getBoInfo()
    {
        $user = Auth::guard('bisnis_owner')->user();

        if (empty($user)) {
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        try {
            $bo_info = BoInfo::where('bisnis_owner_id', $user->id)
                ->first();
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'BoInfo record not found',
                'data' => null
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'success',
            'data' => $bo_info,
        ]);
    }


    public function storeBoIfo(Request $request)
    {
        // Add logging at the beginning of the method
        Log::info('storeBoIfo called', $request->all());

        $validator = Validator::make($request->all(), [
            'businessId' => 'required',
            'businessEmail' => 'required|email',
            'businessType' => 'required',
            'businessName' => 'required',
            'phone' => 'required|numeric',
            'mobile' => 'required|numeric',
            'address' => 'required',
            'province' => 'required',
            'city' => 'required',
            'subdistrict' => 'required',
            'village' => 'required',
            'postal_code' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            Log::error('Validation failed', $errors->toArray());
            return response()->json(['status' => false, 'errors' => $errors], 422);
        }

        $user = Auth::guard('bisnis_owner')->user();
        if (empty($user)) {
            Log::error('User is not authenticated');
            return response()->json([
                'status' => false,
                'message' => 'User is not authenticated'
            ], 401);
        }

        // Log user info and request data before creating BoInfo
        Log::info('User authenticated', ['user_id' => $user->id, 'request_data' => $request->all()]);

        BoInfo::create([
            'bisnis_owner_id' => $user->id,
            'businessId' => 'BO0000' . $request->businessId,
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
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Completed Data Successfully',
        ]);
    }
}
