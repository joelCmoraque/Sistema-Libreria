<?php

namespace App\Http\Controllers;

use App\Models\Input;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Ramsey\Uuid\Uuid;

class ReportController extends Controller
{
    //
    public function Registros (Input $input){
        $registro = Input::where('id', $input->id)->get();
        $pdf = Pdf::loadView('pdf.example',['registro'=>$registro]);
        $name = Uuid::uuid4()->toString();
        return $pdf->download("$name.pdf");
    }

}
