<?php

namespace Database\Seeders;

use App\Helpers\FieldHelper;
use App\Models\Field;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class FillFieldsTable extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Field::insertOrIgnore([
            [
                'name' => __('Theme'),
                'description' => 'Theme of your ticket',
                'type' => FieldHelper::TYPE_TEXT,
                'options' => null,
                'is_default' => true
            ],
            [
                'name' => __('Message'),
                'description' => 'Your ticket text',
                'type' => FieldHelper::TYPE_RICHTEXT,
                'options' => null,
                'is_default' => true
            ],
        ]);
    }
}
