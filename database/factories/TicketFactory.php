<?php

namespace Database\Factories;

use App\Helpdesk\TicketParticipant;
use App\Helpdesk\TicketPriority;
use App\Helpdesk\TicketStatus;
use App\Helpers\FieldHelper;
use App\Models\Category;
use App\Models\Ticket;
use App\Models\TicketFields;
use App\Models\TicketParticipants;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'number' => Carbon::now()->format('ymdhisu'),
            'user_id' => User::all()->random()->id,
            'category_id' => Category::all()->random()->id,
            'subject' => fake()->realTextBetween(88, 160),
            'content' => fake()->realTextBetween(200, 300),
            'status' => fake()->randomElement([TicketStatus::NEW, TicketStatus::IN_APPROVAL]),
            'priority' => fake()->randomElement([TicketPriority::NORMAL, TicketPriority::LOW, TicketPriority::MEDIUM, TicketPriority::HIGH]),
            'need_approval' => fake()->randomElement([0, 1]),
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Ticket $ticket) {
            /** @var Category $category */
            $category = Category::find($ticket->category_id)->load('fields');
            $categoryFields = $category->fields;

            // dd($category);

            if ($categoryFields->count() > 0) {
                foreach ($categoryFields as $field) {
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
                    TicketFields::factory()->state([
                        'ticket_id' => $ticket->id,
                        'category_id' => $ticket->category_id,
                        'field_id' => $field->field_id,
                        'content' => $content
                    ])->create();
                }
            }

            try {
                if ($ticket->need_approval) {
                    TicketParticipants
                        ::factory()
                        ->count(rand(1, 4))
                        ->state([
                            'role' => TicketParticipant::APPROVAL,
                            'ticket_id' => $ticket->id,
                        ])
                        ->create();
                }
                TicketParticipants
                    ::factory()
                    ->count(rand(1, 2))
                    ->state([
                        'role' => TicketParticipant::OBSERVER,
                        'ticket_id' => $ticket->id,
                    ])
                    ->create();
            } catch (\Illuminate\Database\QueryException $exception) {
                echo 'error' . PHP_EOL;
            }
        });
    }
}
