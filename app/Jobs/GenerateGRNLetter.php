<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\GoodReceiptNote;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GenerateGRNLetter implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $grnData;

    public function __construct($grnData)
    {
        $this->grnData = $grnData;
    }

    public function handle()
    {
        Log::info('JOBS GRN CALLED');
        try {
            $pdf = Pdf::loadView('grn-template', ['grnData' => $this->grnData]);
            $pdfContent = $pdf->output();

            $fileName = $this->grnData['grn_id'] . '-' . Str::uuid() . '.pdf';
            Storage::disk('s3')->put($fileName, $pdfContent, 'public');
            $url = Storage::disk('s3')->url($fileName);

            GoodReceiptNote::where('grn_id', $this->grnData['grn_id'])->first()->update([
                'url_file' => $url
            ]);

            Log::info('PDF generated and uploaded successfully:', ['url' => $url]);
        } catch (\Exception $e) {
            Log::error('Failed to generate and upload PDF:', ['error' => $e->getMessage()]);
        }
    }
}
