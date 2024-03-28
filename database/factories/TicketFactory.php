<?php

namespace Database\Factories;

use App\Helpdesk\Participant;
use App\Helpdesk\TicketPriority;
use App\Helpdesk\TicketStatus;
use App\Helpdesk\TicketThreadType;
use App\Helpers\FieldHelper;
use App\Models\Category;
use App\Models\Department;
use App\Models\Ticket;
use App\Models\TicketFields;
use App\Models\TicketParticipant;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
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
        /** @var Department $dep */
        $dep = Department::all()->random();
        return [
            'user_id' => User::all()->random()->id,
            'department_id' => $dep->id,
            'category_id' => $dep->categories->random()->id,
            'subject' => fake()->realTextBetween(16, 55),
            'content' => fake()->realTextBetween(100, 200),
            'status' => fake()->numberBetween(TicketStatus::NEW, TicketStatus::APPROVED),
            'priority' => fake()->randomElement([TicketPriority::NORMAL, TicketPriority::LOW, TicketPriority::MEDIUM, TicketPriority::HIGH]),
            'need_approval' => fake()->randomElement([0, 1]),
            'created_at' => fake()->dateTimeBetween('-1year', 'now')
        ];
    }

    public function configure(): static
    {
        return $this
            ->afterMaking(function (Ticket $ticket) {

            })
            ->afterCreating(function (Ticket $ticket) {
                $ticket->department_id = $ticket->category->department_id;
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
                $ticket->refresh();
                try {
                    if ($ticket->need_approval) {
                        TicketParticipant
                            ::factory()
                            ->count(rand(1, 4))
                            ->state([
                                'role' => Participant::APPROVAL,
                                'ticket_id' => $ticket->id,
                            ])
                            ->create();
                    }
                    $members = $ticket->department->members;
                    if (!empty($members)) {
                        foreach ($members as $member) {
                            $y = $this->faker->boolean(40);
                            if ($y) {
                                TicketParticipant
                                    ::factory()
                                    ->count(1)
                                    ->state([
                                        'user_id' => $member->id,
                                        'role' => Participant::ASSIGNEE,
                                        'ticket_id' => $ticket->id,
                                    ])
                                    ->create();
                            }

                        }
                    }
                    TicketParticipant
                        ::factory()
                        ->count(rand(1, 2))
                        ->state([
                            'role' => Participant::OBSERVER,
                            'ticket_id' => $ticket->id,
                        ])
                        ->create();
                } catch (\Illuminate\Database\QueryException $exception) {
                    dump($exception->getMessage());
                }

                try {
                    // load ticket approvals
                    $approvals = $ticket->approvals;
                    if ($approvals->isNotEmpty()) {
                        foreach ($approvals as $approval) {
                            TicketThread
                                ::factory()
                                ->count(1)
                                ->state(
                                    [
                                        'ticket_id' => $ticket->id,
                                        'user_id' => $approval->user_id,
                                        'type' => Arr::random([
                                            TicketThreadType::APPROVE_COMMENT->value,
                                            TicketThreadType::DECLINE_COMMENT->value
                                        ]),
                                        'created_at' => fake()->dateTimeBetween($ticket->created_at)
                                    ]
                                )
                                ->create();
                        }

                    }

                    // load ticket observers
                    $observers = $ticket->observers;
                    if ($observers->isNotEmpty()) {
                        foreach ($observers as $observer) {
                            TicketThread
                                ::factory()
                                ->count(1)
                                ->state(
                                    [
                                        'ticket_id' => $ticket->id,
                                        'user_id' => $observer->user_id,
                                        'type' => TicketThreadType::COMMENT->value,
                                        'created_at' => fake()->dateTimeBetween($ticket->created_at)
                                    ]
                                );
                        }
                    }

                    // only requester comment
                    TicketThread
                        ::factory()
                        ->count(1)
                        ->state(
                            [
                                'ticket_id' => $ticket->id,
                                'user_id' => $ticket->requester->id,
                                'type' => TicketThreadType::COMMENT->value,
                                'created_at' => fake()->dateTimeBetween($ticket->created_at)
                            ]
                        );

                } catch (\Exception $e) {
                    dump($e->getMessage());
                }
            });
    }
}
