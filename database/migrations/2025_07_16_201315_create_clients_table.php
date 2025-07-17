<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('user_id');

        $table->enum('type', ['individual', 'business'])->default('individual');
        $table->string('name');
        $table->string('company_name')->nullable();
        $table->string('contact_person')->nullable();
        $table->string('email')->nullable();

        $table->string('phone_personal')->nullable();
        $table->string('phone_business')->nullable();
        $table->string('phone_extension')->nullable();

        // Billing Address
        $table->string('billing_address_line1')->nullable();
        $table->string('billing_address_line2')->nullable();
        $table->string('billing_city')->nullable();
        $table->string('billing_state')->nullable();
        $table->string('billing_postal_code')->nullable();
        $table->string('billing_country')->nullable();

        // Shipping Address
        $table->string('shipping_address_line1')->nullable();
        $table->string('shipping_address_line2')->nullable();
        $table->string('shipping_city')->nullable();
        $table->string('shipping_state')->nullable();
        $table->string('shipping_postal_code')->nullable();
        $table->string('shipping_country')->nullable();

        $table->string('tax_number')->nullable();
        $table->string('currency')->nullable();
        $table->text('notes')->nullable();

        $table->boolean('receive_email')->default(true);
        $table->boolean('is_active')->default(true);

        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
