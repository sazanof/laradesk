<?php

namespace Database\Factories;

use App\Helpdesk\Participant;
use App\Models\Ticket;
use App\Models\TicketParticipant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class TicketParticipantsFactory extends Factory
{
    protected $model = TicketParticipant::class;

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
            'role' => fake()->randomElement([Participant::REQUESTER, Participant::APPROVAL, Participant::OBSERVER])
        ];
    }
}
