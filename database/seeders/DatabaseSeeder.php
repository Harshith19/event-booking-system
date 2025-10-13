<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Event;
use App\Models\Payment;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create 2 admins
        User::factory()->admin()->count(2)->create();

        // Create 3 organizers
        $organizers = User::factory()->organizer()->count(3)->create();

        // Create 10 customers
        $customers = User::factory()->customer()->count(10)->create();

        // Create 5 events
        $events = Event::factory()->count(5)->create([
            'created_by' => function () use ($organizers) {
                return $organizers->random()->id;
            },
        ]);

        // Create 15 tickets for the events
        $tickets = Ticket::factory()->count(15)->create([
            'event_id' => function () use ($events) {
                return $events->random()->id;
            },
        ]);

        // Logic 1 - Create 20 bookings for the tickets by customers
        $bookings = Booking::factory()->count(20)->create([
            'user_id' => function () use ($customers) {
                return $customers->random()->id;
            },
            'ticket_id' => function () use ($tickets) {
                return $tickets->random()->id;
            },
        ]);

        // Logic 2 - Create 20 unique bookings (Logic 1 may throw duplicate error)
        /* $bookingPairs = collect();
        $bookings = collect();

        while ($bookings->count() < 20) {
            $user = $customers->random();
            $ticket = $tickets->random();
            $pairKey = $user->id . '-' . $ticket->id;

            if (!$bookingPairs->contains($pairKey)) {
                $bookingPairs->push($pairKey);

                $booking = Booking::factory()->create([
                    'user_id' => $user->id,
                    'ticket_id' => $ticket->id,
                    'quantity' => rand(1, 5),
                    'status' => 'pending',
                ]);

                $bookings->push($booking);
            }
        } */

        // Create payments for some bookings
        Payment::factory()->count(20)->create([
            'booking_id' => function () use ($bookings) {
                return $bookings->random()->id;
            },
        ]);
    }
}
