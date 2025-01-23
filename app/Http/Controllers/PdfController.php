<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PdfController extends Controller
{
    //

    public function downloadPdf()
    {
        $pdfPath = session('pdf_path');

        if ($pdfPath && file_exists($pdfPath)) {
            return response()->download($pdfPath)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'El archivo PDF no está disponible.');
    }
}
