<?php

namespace Database\Factories;

use App\Helpers\FieldHelper;
use App\Models\Category;
use App\Models\Field;
use App\Models\Ticket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketFields>
 */
class TicketFieldsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $field = Field::all()->random();
        $content = '';
        switch ($field->type) {
            case FieldHelper::TYPE_FILE:
                $content = fake()->filePath();
                break;
            case FieldHelper::TYPE_RICHTEXT:
            case FieldHelper::TYPE_TEXT:
            case FieldHelper::TYPE_TEXTAREA:
                $content = fake()->realTextBetween(40, 100);
                break;
            case FieldHelper::TYPE_CHECKBOX:
            case FieldHelper::TYPE_DROPDOWN:
                $content = fake()->randomNumber(2);
        }
        return [
            'ticket_id' => Ticket::all()->random()->id,
            'category_id' => Category::all()->random()->id,
            'field_id' => $field->id,
            'content' => $content
        ];
    }
}
