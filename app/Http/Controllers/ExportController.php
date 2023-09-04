<?php

namespace App\Http\Controllers;

use App\Jobs\ExportJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Matrix\Exception;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class ExportController extends Controller
{
    public function exportTickets(Request $request)
    {
        ExportJob::dispatch($request->all());
        return response()->json(['success' => true]);
    }

    public function downloadExportFile($fileName)
    {
        try {
            $file = Storage::path('private/export/' . $fileName);
            $headers = array(
                'Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            );
            return Response::download($file, $fileName, $headers)->deleteFileAfterSend();
        } catch (Exception $exception) {
            return null;
        }

    }
}
