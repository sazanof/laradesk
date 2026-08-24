<?php

namespace App\Console\Commands;

use App\Libraries\BotAPI;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

#[Signature('tdm:notification {--workplace-id=-1} {--to=} {--message=}')]
#[Description('Add TDM notifications')]
class AddTDMNotification extends Command
{
    protected BotAPI $api;
    protected ?int $workplaceId = null;
    protected ?int $to = null;
    protected ?string $message = null;

    public function __construct(BotAPI $api)
    {
        parent::__construct();
        $this->api = $api;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->workplaceId = $this->option('workplace-id');
        $this->to = $this->option('to');
        $this->message = $this->option('message');
        //dd($this->message);
        try {
            $this->api->sendHtml(
                workspaceId: $this->workplaceId,
                groupId: $this->to,
                html: $this->message
            );
        } catch (\Throwable $th) {
            Log::error($th->getMessage());
        }
    }
}
