<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function downloadExcel(){
        $filePath = 'public/env/download/PRE-CARGUE 18062024.xlsm';

        if (!Storage::exists($filePath)) {
            return abort(404, 'File not found.');
        }

        return Storage::download($filePath, 'PRE-CARGUE 18062024.xlsm');
    }
}