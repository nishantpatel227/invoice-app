<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Clients/Index', [
            'clients' => $clients,
        ]);
    }

    public function create()
    {
        return Inertia::render('Clients/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:individual,business',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_personal' => 'nullable|string|max:20',
            'phone_business' => 'nullable|string|max:20',
            'phone_extension' => 'nullable|string|max:10',

            // Billing
            'billing_address_line1' => 'nullable|string|max:255',
            'billing_address_line2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',

            // Shipping
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',

            'tax_number' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'receive_email' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $validated['user_id'] = auth()->id();

        Client::create($validated);

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        $this->authorizeClient($client);

        return Inertia::render('Clients/Show', [
            'client' => $client,
        ]);
    }

    public function edit(Client $client)
    {
        $this->authorizeClient($client);

        return Inertia::render('Clients/Edit', [
            'client' => $client,
        ]);
    }

    public function update(Request $request, Client $client)
    {
        $this->authorizeClient($client);

        $validated = $request->validate([
            'type' => 'required|in:individual,business',
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_personal' => 'nullable|string|max:20',
            'phone_business' => 'nullable|string|max:20',
            'phone_extension' => 'nullable|string|max:10',

            // Billing
            'billing_address_line1' => 'nullable|string|max:255',
            'billing_address_line2' => 'nullable|string|max:255',
            'billing_city' => 'nullable|string|max:100',
            'billing_state' => 'nullable|string|max:100',
            'billing_postal_code' => 'nullable|string|max:20',
            'billing_country' => 'nullable|string|max:100',

            // Shipping
            'shipping_address_line1' => 'nullable|string|max:255',
            'shipping_address_line2' => 'nullable|string|max:255',
            'shipping_city' => 'nullable|string|max:100',
            'shipping_state' => 'nullable|string|max:100',
            'shipping_postal_code' => 'nullable|string|max:20',
            'shipping_country' => 'nullable|string|max:100',

            'tax_number' => 'nullable|string|max:50',
            'currency' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
            'receive_email' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $client->update($validated);

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        $this->authorizeClient($client);

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted successfully.');
    }

    private function authorizeClient(Client $client)
    {
        if ($client->user_id !== auth()->id()) {
            abort(403);
        }
    }
}
