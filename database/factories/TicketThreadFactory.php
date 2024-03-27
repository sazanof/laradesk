<?php

namespace Database\Factories;

use App\Helpdesk\TicketThreadType;
use App\Models\Ticket;
use App\Models\TicketThread;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TicketThread>
 */
class TicketThreadFactory extends Factory
{
    protected $model = TicketThread::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::all()->random()->id,
            'user_id' => User::all()->random()->id,
            'type' => fake()->randomElement([
                TicketThreadType::DECLINE_COMMENT->value,
                TicketThreadType::COMMENT->value,
                TicketThreadType::APPROVE_COMMENT->value,
                TicketThreadType::CLOSE_COMMENT->value,
                TicketThreadType::SOLVED_COMMENT->value,
                TicketThreadType::REOPEN_COMMENT->value,
            ]),
            'content' => fake()->realTextBetween(30, 180),
        ];
    }
}
