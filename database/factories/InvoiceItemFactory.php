<?php

namespace Database\Factories;

use App\Models\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $quantity = $this->faker->randomFloat(2, 1, 10);
        $rate = $this->faker->randomFloat(2, 10, 100);
        $amount = round($quantity * $rate, 2);

        return [
            'description' => $this->faker->sentence(4),
            'quantity' => $quantity,
            'rate' => $rate,
            'amount' => $amount,
        ];
    }
}
