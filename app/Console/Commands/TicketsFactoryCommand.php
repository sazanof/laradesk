<?php

namespace App\Console\Commands;

use App\Models\Ticket;
use App\Models\TicketFields;
use App\Models\TicketParticipants;
use App\Models\TicketThread;
use Database\Factories\TicketFactory;
use Illuminate\Console\Command;

class TicketsFactoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helpdesk:fake-tickets {count}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete all tickets data and fill with faker one';

    protected int $count = 1;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->count = $this->argument('count');
        $this->error('!!IF YOU CONTINUE, THIS DELETE ALL TICKETS DATA IN YOUR SYSTEM!!!');
        if ($this->confirm('Fill tables with fake data and DELETE CURRENT DATA?')) {
            TicketParticipants::truncate();
            TicketFields::truncate();
            TicketThread::truncate();
            Ticket::truncate();

            $limit = 10;
            $total = $this->count;
            $pages = $total / $limit;

            for ($i = 0; $i < $pages; $i++) {
                Ticket::factory()->count($limit)->create();
            }


        }
    }
}
