<?php

namespace App\Http\Controllers;

use App\Models\Fasyankes;
use App\Models\LegalDocBo;
use App\Models\LegalDocFasyankes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use setasign\FpdiProtection\FpdiProtection;

class LegalDocController extends Controller
{
    public function getLegalDoc()
    {
        Log::info('Entering getLegalDoc method');
        $bo = Auth::guard('bisnis_owner')->user();
        $legal = LegalDocBo::where('bisnis_owner_id', $bo->id)
            ->where('status', 'approved')
            ->first();
        if (!$legal) {
            Log::warning('Legal document not found for bisnis_owner_id: ' . $bo->id);
            return response()->json([
                'status' => false,
                'message' => 'Data legal doc not found'
            ], 404);
        }
        Log::info('Legal document found for bisnis_owner_id: ' . $bo->id);
        return response()->json(['status' => true, 'message' => 'Success get legal doc', 'data' => $legal]);
    }

    private function validateFiles(Request $request, $type)
    {
        Log::info('Entering validateFiles method with type: ' . $type);
        $maxFileSize = 2000;
        $rules = [
            'iso' => 'nullable|file|mimes:pdf|max:' . $maxFileSize,
            'password' => [
                'required', 'string', 'min:8', 'regex:/[A-Z]/', 'regex:/[!@#$%^&*(),.?":{}|<>_]/', 'regex:/[0-9]/'
            ],
            [
                'password.regex' => 'Password must contain at least 1 Uppercase Word, 1 Special Character, and 1 Number',
            ]
        ];
        $messages = [
            'password.regex' => 'Password must contain at least 1 uppercase letter, 1 special character, and 1 number.',
        ];

        if ($type === 'Perusahaan') {
            $rules = array_merge($rules, [
                'ktp' => 'required|file|mimes:pdf|max:' . $maxFileSize,
                'npwp' => 'required|file|mimes:pdf|max:' . $maxFileSize,
                'akta' => 'required|file|mimes:pdf|max:' . $maxFileSize,
                'sk_kemenkumham' => 'required|file|mimes:pdf|max:' . $maxFileSize,
                'nib' => 'required|file|mimes:pdf|max:' . $maxFileSize,
            ]);
        } elseif ($type === 'Apotek') {
            $rules = array_merge($rules, [
                'sia' => 'required|file|mimes:pdf|max:' . $maxFileSize,
                'sipa' => 'required|file|mimes:pdf|max:' . $maxFileSize,
            ]);
        } elseif ($type === 'Klinik') {
            $rules = array_merge($rules, [
                'simk' => 'required|file|mimes:pdf|max:' . $maxFileSize,
                'siok' => 'required|file|mimes:pdf|max:' . $maxFileSize,
            ]);
        } elseif ($type === 'Perorangan') {
            $rules = array_merge($rules, [
                'ktp' => 'required|file|mimes:pdf|max:' . $maxFileSize,
                'npwp' => 'required|file|mimes:pdf|max:' . $maxFileSize,
            ]);
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = collect($validator->errors())->map(function ($messages) {
                return $messages[0];
            });
            Log::warning('Validation failed: ' . json_encode($errors));
            return response()->json(['status' => false, "message" => 'Upload Legal Document Failed', 'errors' => $errors], 422);
        }

        Log::info('Validation passed');
        return true;
    }

    private function uploadAndEncryptFiles(Request $request, $name)
    {
        Log::info('Entering uploadAndEncryptFiles method');
        $uploadedFileUrls = [];

        $str = Str::random(60);
        foreach ($request->file() as $key => $file) {
            Log::info('Processing file: ' . $key);
            $tempFilePath = $file->storeAs('temp', $key . '_' . $name . '_' . $str . '.' . $file->getClientOriginalExtension());
            Log::info('tempfile : ' . $tempFilePath);
            if ($request->has('password')) {
                $password = $request->input('password');
                $pdf = new FpdiProtection();
                Log::info('password : ' . $password);

                $sourceFile = storage_path('app/' . $tempFilePath);
                if (!file_exists($sourceFile)) {
                    Log::warning('Source file does not exist: ' . $sourceFile);
                    continue;
                }
                $pageCount = $pdf->setSourceFile($sourceFile);

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplId = $pdf->importPage($pageNo);
                    $specs = $pdf->getTemplateSize($tplId);

                    $orientation = ($specs['width'] > $specs['height']) ? 'L' : 'P';
                    $pdf->AddPage($orientation, [$specs['width'], $specs['height']]);
                    $pdf->useTemplate($tplId);
                }

                $ownerPassword = 'Idsmartcare2024@';
                $pdf->SetProtection([], $password, $ownerPassword);

                $protectedFilePath = $key . '_' . $name . '_' . $str . '.' . $file->getClientOriginalExtension();
                $outputPath = storage_path('app/temp/' . $protectedFilePath);
                $pdf->Output($outputPath, 'F');

                Storage::disk('s3')->put($protectedFilePath, file_get_contents($outputPath), 'public');
                $uploadedFileUrls[$key] = Storage::disk('s3')->url($protectedFilePath);

                Storage::delete($tempFilePath);
                Storage::delete('temp/' . $protectedFilePath);
                Log::info("$key uploaded and encrypted to: " . $uploadedFileUrls[$key]);
                Log::info($uploadedFileUrls[$key]);
            }
        }

        Log::info('File upload and encryption completed');
        return $uploadedFileUrls;
    }

    public function upload(Request $request)
    {
        Log::info('Entering upload method');
        $validation = $this->validateFiles($request, $request->type);
        if ($validation instanceof \Illuminate\Http\JsonResponse) {
            Log::info('Validation errors in upload method');
            return $validation; // Return validation errors response
        }

        $bo = Auth::guard('bisnis_owner')->user();
        if (empty($bo)) {
            Log::warning('User is not authenticated');
            return response()->json([
                'message' => 'User is not authenticated',
            ], 401);
        }

        $name = Str::slug($bo->name);

        try {
            Log::info('Calling uploadAndEncryptFiles method');
            $uploadedFileUrls = $this->uploadAndEncryptFiles($request, $name);

            Log::info($uploadedFileUrls);
            $legalDoc = LegalDocBo::create([
                'bisnis_owner_id' => $bo->id,
                'ktp' => $uploadedFileUrls['ktp'] ?? null,
                'akta' => $uploadedFileUrls['akta'] ?? null,
                'sk_kemenkumham' => $uploadedFileUrls['sk_kemenkumham'] ?? null,
                'npwp' => $uploadedFileUrls['npwp'] ?? null,
                'nib' => $uploadedFileUrls['nib'] ?? null,
                'iso' => $uploadedFileUrls['iso'] ?? null,
                'status' => 'apply',
            ]);

            Log::info('Legal document created successfully');
            return response()->json([
                'message' => 'Upload Legal Document Successfully',
                'files' => $uploadedFileUrls,
                'legal_doc' => $legalDoc,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error uploading and encrypting files: ' . $e->getMessage());
            return response()->json([
                'message' => 'Upload Legal Document Failed',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadLegalFasyankes(Request $request)
    {
        Log::info('Entering uploadLegalFasyankes method');
        $validation = $this->validateFiles($request, $request->type);
        if ($validation instanceof \Illuminate\Http\JsonResponse) {
            Log::info('Validation errors in uploadLegalFasyankes method');
            return $validation; // Return validation errors response
        }
        $getFasyankes = Fasyankes::where('fasyankesId', $request->fasyankes_id)->first();
        if (empty($getFasyankes)) {
            Log::warning('Fasyankes not found for fasyankes_id: ' . $request->fasyankes_id);
            return response()->json([
                'message' => 'Fasyankes not found',
            ], 404);
        }

        $name = Str::slug($getFasyankes->name);

        try {
            Log::info('Calling uploadAndEncryptFiles method');
            $uploadedFileUrls = $this->uploadAndEncryptFiles($request, $name);

            $legalDoc = LegalDocFasyankes::create([
                'fasyankes_id' => $getFasyankes->fasyankesId,
                'sia' => $uploadedFileUrls['sia'] ?? null,
                'sipa' => $uploadedFileUrls['sipa'] ?? null,
                'simk' => $uploadedFileUrls['simk'] ?? null,
                'siok' => $uploadedFileUrls['siok'] ?? null,
            ]);

            Log::info('Legal document created successfully for Fasyankes');
            return response()->json([
                'message' => 'Upload Legal Document Successfully',
                'files' => $uploadedFileUrls,
                'legal_doc' => $legalDoc,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Error uploading and encrypting files for Fasyankes: ' . $e->getMessage());
            return response()->json([
                "message" => "Upload Legal Document Failed",
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
