<?php

namespace App\Jobs;

use App\Events\ExportFinishedEvent;
use App\Exports\TicketsExport;
use App\Helpdesk\WebsocketClient;
use App\Helpers\ExportRequest;
use App\Helpers\RequestBuilder;
use App\Mail\ExportReadyMail;
use App\Models\Ticket;
use App\Models\User;
use App\Notifications\ExportFinishedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Writer\Exception;

class ExportJob implements ShouldQueue, ShouldBeUnique
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Request $request;
    protected RequestBuilder $builder;
    const ROOT_PATH = 'private/export/';

    public $uniqueFor = 3600;

    private string $exportPath;
    private string $filename;

    protected ?int $connId = null;
    protected ?int $userId = null;
    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(array $request)
    {
        $this->filename = 'export_' . date('Ymdhis') . '.' .
            Str::lower(\Maatwebsite\Excel\Excel::XLSX);
        $this->exportPath = self::ROOT_PATH . $this->filename;
        $this->request = new Request();
        $this->request->merge($request);
        $this->connId = $this->request->get('conn_id');
        $this->userId = $this->request->get('user_id');
        $this->user = User::find($this->userId);
    }

    public function uniqueId(): string
    {
        return $this->userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->builder = RequestBuilder::results($this->request, Ticket::with([
            'fields',
            'category',
            'department',
            'requester',
            'assignees',
            'observers',
            'approvals'
        ]));
        try {
            Excel::store(new TicketsExport($this->builder->get()), $this->exportPath);
            $this->user->notify(new ExportFinishedNotification($this->filename));
        } catch (Exception $e) {
            Log::error(sprintf('[WS] %s', $e->getMessage()));
        }

        //Todo notification via mail if mail enabled


    }
}
