<?php

namespace Database\Factories;

use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['individual', 'business']);

        return [
            'user_id' => User::inRandomOrder()->first()->id ?? 1,

            'type' => $type,
            'name' => $type === 'individual'
                ? $this->faker->name()
                : $this->faker->company(),

            'company_name' => $type === 'business' ? $this->faker->company() : null,
            'contact_person' => $type === 'business' ? $this->faker->name() : null,
            'email' => $this->faker->unique()->safeEmail(),

            'phone_personal' => $this->faker->phoneNumber(),
            'phone_business' => $this->faker->phoneNumber(),
            'phone_extension' => $this->faker->optional()->numerify('###'),

            // Billing address
            'billing_address_line1' => $this->faker->streetAddress(),
            'billing_address_line2' => $this->faker->optional()->secondaryAddress(),
            'billing_city' => $this->faker->city(),
            'billing_state' => $this->faker->state(),
            'billing_postal_code' => $this->faker->postcode(),
            'billing_country' => $this->faker->country(),

            // Shipping address
            'shipping_address_line1' => $this->faker->streetAddress(),
            'shipping_address_line2' => $this->faker->optional()->secondaryAddress(),
            'shipping_city' => $this->faker->city(),
            'shipping_state' => $this->faker->state(),
            'shipping_postal_code' => $this->faker->postcode(),
            'shipping_country' => $this->faker->country(),

            'tax_number' => $this->faker->optional()->bothify('TAX-#######'),
            'currency' => $this->faker->currencyCode(),
            'notes' => $this->faker->optional()->sentence(),

            'receive_email' => $this->faker->boolean(80),
            'is_active' => $this->faker->boolean(90),
        ];
    }
}
