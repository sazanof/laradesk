<?php

namespace Database\Factories;

use App\Helpdesk\TicketParticipant;
use App\Models\Ticket;
use App\Models\TicketParticipants;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TicketParticipantsFactory extends Factory
{
    protected $model = TicketParticipants::class;

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
            'role' => fake()->randomElement([TicketParticipant::REQUESTER, TicketParticipant::APPROVAL, TicketParticipant::OBSERVER])
        ];
    }
}
