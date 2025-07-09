<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
             'user_id' => User::factory(), // or pass a real user ID in seeder
        'from_name' => $this->faker->company,
        'to_name' => $this->faker->name,
        'invoice_number' => 'INV-' . str_pad($this->faker->unique()->numberBetween(1, 99999), 5, '0', STR_PAD_LEFT),
        'date' => $this->faker->date(),
        'due_date' => $this->faker->date(),
        'subtotal' => $this->faker->randomFloat(2, 100, 1000),
        'tax_percent' => $this->faker->numberBetween(0, 15),
        'discount' => $this->faker->randomFloat(2, 0, 50),
        'shipping' => $this->faker->randomFloat(2, 0, 50),
        'total' => 0, // we'll calculate it below
        'notes' => $this->faker->sentence(),
        'terms' => $this->faker->sentence(),
        'status' => $this->faker->randomElement(['draft', 'sent', 'paid']),
        ];
    }
}
