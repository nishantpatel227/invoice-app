<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\User;

class InvoiceSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first() ?? User::factory()->create(); // Use existing or fake user

        Invoice::factory()
            ->count(20)
            ->for($user)
            ->has(
                InvoiceItem::factory()->count(3), // each invoice gets 3 items
                'items'
            )
            ->create()
            ->each(function ($invoice) {
                $subtotal = $invoice->items->sum('amount');
                $tax = ($subtotal * $invoice->tax_percent) / 100;
                $discount = $invoice->discount;
                $shipping = $invoice->shipping;
                $invoice->total = round($subtotal + $tax + $shipping - $discount, 2);
                $invoice->subtotal = $subtotal;
                $invoice->save();
            });
    }
}
