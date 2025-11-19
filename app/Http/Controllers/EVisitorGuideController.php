<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class EVisitorGuideController extends Controller
{
    /**
     * Stream the exhibitor directory PDF from storage after basic auth check.
     */
    public function showPdf(Request $request)
    {
        if (!session('allow_exhibitor_pdf')) {
            abort(403, 'PDF access not allowed.');
        }

        $path = storage_path('app/private/docs/BTS-2025_Exhibitor-Directory.pdf');

        if (!file_exists($path)) {
            abort(404);
        }

        session()->forget('allow_exhibitor_pdf');

        return Response::file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="BTS-2025_Exhibitor-Directory.pdf"'
        ]);
    }
}

