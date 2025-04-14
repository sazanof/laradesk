<?php

namespace App\Http\Controllers;

use App\Helpers\AclHelper;
use App\Jobs\ExportJob;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Matrix\Exception;
use Dompdf\Dompdf;

class ExportController extends Controller
{
    public function exportTickets(Request $request)
    {
        ExportJob::dispatch($request->all());
        return response()->json(['success' => true]);
    }

    public function exportTicketsPdf($id, Request $request)
    {
        $ticket = Ticket::find($id);
        $user = $request->user();
        if (AclHelper::userHasAccessToTicket($ticket, $user)) {
            $dompdf = new Dompdf(['chroot' => base_path()]);
            $dompdf->setBasePath(base_path());
            // (Optional) set up the paper size and orientation
            $dompdf->setPaper('A4');
            $html = view('export.ticket_pdf', [
                'ticket' => $ticket->load([
                    'department',
                    'category',
                    'requester',
                    'assignees',
                    'approvals',
                    'observers',
                    'thread',
                    'files'
                ])
            ])->render();

            $dompdf->loadHtml($html);
            // Render the HTML as PDF
            $dompdf->render();
            // Output the generated PDF to Browser
            $dompdf->stream('Ticket_' . Str::padLeft($ticket->id, 10, '0'),
                [
                    "Attachment" => true
                ]);
        }
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
