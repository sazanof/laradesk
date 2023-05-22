<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;

class FillConfigTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Config::insertOrIgnore([
            [
                'key' => 'app.bg',
                'value' => '/storage/bg/default-bg.jpg',
                'description' => 'Default application background image'
            ],
            [
                'key' => 'app.name',
                'value' => 'HelpDesk',
                'description' => 'Application name'
            ]
        ]);
    }
}
