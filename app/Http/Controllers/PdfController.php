<?php

namespace App\Http\Controllers;

use App\Models\bill;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController
{
    function viewpdf($request)
    {
        $bo=bill::where('id', $request)->first();
        return view('recepit', compact('bo'));
    }

    function dopdf($request)
    {
        $bo=bill::where('id', $request)->first();
        $pdf = PDF::loadView('recepit1', compact('bo'));
        return $pdf->download('receipt.pdf');
    }

}
