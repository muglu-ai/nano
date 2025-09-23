<?php

namespace Database\Factories;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'payment_method' => $this->faker->word(),
            'amount' => $this->faker->randomFloat(),
            'amount_received' => $this->faker->randomFloat(),
            'transaction_id' => $this->faker->word(),
            'pg_result' => $this->faker->word(),
            'track_id' => $this->faker->word(),
            'response' => $this->faker->word(),
            'pg_response_json' => $this->faker->words(),
            'payment_date' => Carbon::now(),
            'status' => $this->faker->word(),
            'order_id' => $this->faker->word(),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'amount_paid' => $this->faker->randomFloat(),
            'currency' => $this->faker->word(),
            'rejection_reason' => $this->faker->word(),
            'receipt_image' => $this->faker->word(),

            'invoice_id' => Invoice::factory(),
            'user_id' => User::factory(),
        ];
    }
}
