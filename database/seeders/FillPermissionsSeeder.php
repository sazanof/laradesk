<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use App\Helpers\AclHelper;


class FillPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::insertOrIgnore([
            [
                'entity' => AclHelper::TICKETS,
                'operation' => AclHelper::CREATE,
                'description' => 'Create tickets'
            ],
            [
                'entity' => AclHelper::TICKETS,
                'operation' => AclHelper::READ,
                'description' => 'View tickets'
            ],
            [
                'entity' => AclHelper::TICKETS,
                'operation' => AclHelper::UPDATE,
                'description' => 'Edit tickets'
            ],
            [
                'entity' => AclHelper::TICKETS,
                'operation' => AclHelper::DELETE,
                'description' => 'Edit tickets'
            ]
        ]);
    }
}
