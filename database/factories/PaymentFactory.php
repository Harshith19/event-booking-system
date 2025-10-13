<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $booking = Booking::factory()->create();

        return [
            'booking_id' => $booking->id,
            'amount' => $booking->total_amount,
            'status' => $this->faker->randomElement(['success', 'failed']),
            'transaction_id' => 'TXN_' . $this->faker->uuid(),
        ];
    }
}
