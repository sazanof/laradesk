<?php

namespace App\Console\Commands;

use App\Models\Office;
use Illuminate\Console\Command;

class AddOfficeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helpdesk:office {--mode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ads an office to system';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = $this->options();
        $mode = $options['mode'];
        if ($mode === 'add') {
            $name = $this->ask(__('Please type office name'));
            $address = $this->ask(__('Please type address'));
            $office = Office::create(compact('name', 'address'));
            $this->info(__('Office :name created with ID :id', ['name' => $office->name, 'id' => $office->id]));
        } elseif ($mode === 'delete') {
            $offices = Office::select(['id', 'name', 'address'])->orderBy('id')->get();
            if (count($offices) === 0) {
                $this->error(__('No offices in system'));
                return false;
            }
            $this->table(['ID', 'Name', 'Address'], $offices);
            $id = $this->ask(__('Choose ID to DELETE office'));
            if (Office::find($id)->delete()) {
                $this->info(__('Office deleted'));
            }

        } elseif ($mode === 'list') {
            $offices = Office::select(['id', 'name', 'address'])->orderBy('id')->get();
            $this->table(['ID', 'Name', 'Address'], $offices);
        }
        return self::SUCCESS;
    }
}
