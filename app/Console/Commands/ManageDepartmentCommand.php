<?php

namespace App\Console\Commands;

use App\Models\Department;
use App\Models\Office;
use Illuminate\Console\Command;

class ManageDepartmentCommand extends Command
{
    protected const SEPARATOR = '===========================';
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'helpdesk:department {--mode=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage departments';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $options = $this->options();
        $mode = $options['mode'];
        $this->info(self::SEPARATOR);
        switch ($mode) {
            case 'add':
                $name = $this->ask('Enter department name');
                $description = $this->ask('Enter department description');
                $department = Department::create([
                    'name' => $name,
                    'description' => $description
                ]);
                $this->info(sprintf('Department with name %s created', $department->name));

                $this->listDepartments();

                break;
            case 'edit':
                $this->listDepartments();
                $id = $this->ask('Enter department ID you want to edit');
                if (is_null($id)) {
                    return self::FAILURE;
                }
                $name = $this->ask(sprintf('Enter new name for id %d (or leave empty)', $id));
                $description = $this->ask(sprintf('Enter new description for id %d (or leave empty)', $id));
                $department = Department::find($id);
                if (!is_null($name)) {
                    $department->name = $name;
                }
                if (!is_null($description)) {
                    $department->description = $description;
                }
                $department->save();
                $this->listDepartments();
                break;
            case 'list':
                $this->listDepartments();
                break;
        }
        $this->info(self::SEPARATOR);
        return self::SUCCESS;
    }

    protected function listDepartments()
    {
        $departments = Department
            ::select(['id', 'name', 'description'])
            ->orderBy('id')
            ->get();
        $this->table(['ID', 'Name', 'Description'], $departments);
    }
}
