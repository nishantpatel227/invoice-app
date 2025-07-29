<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Barryvdh\Snappy\Facades\SnappyPdf;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;


class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::where('user_id', auth()->id())
            ->with('client')
            ->latest()
            ->get();
            

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function create()
    {
       $clients = Client::where('user_id', auth()->id())->get();
        return Inertia::render('Invoices/Create', [
            'user' => \Illuminate\Support\Facades\Auth::user(),
            'nextInvoiceNumber' => 'INV-' . str_pad(Invoice::max('id') + 1, 5, '0', STR_PAD_LEFT),
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_name' => 'nullable|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'invoice_number' => 'required|string|max:255',
            'date' => 'nullable|date',
            'due_date' => 'nullable|date',
            'payment_terms' => 'nullable|string|max:255',
            'po_number' => 'nullable|string|max:255',
            'subtotal' => 'required|numeric',
            'tax_percent' => 'required|numeric|min:0',
            'discount' => 'required|numeric|min:0',
            'shipping' => 'required|numeric|min:0',
            'total' => 'required|numeric',
            'notes' => 'nullable|string',
            'terms' => 'nullable|string',
            'status' => 'required|in:draft,sent,paid',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:0',
            'items.*.rate' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
        ]);

        // Mass-assign safe fields
        $invoice = Invoice::create([
            'user_id' => auth()->id(),
            'from_name' => $validated['from_name'] ?? '',
            'client_id' => $request->client_id,
            'invoice_number' => (string) $validated['invoice_number'],
            'date' => $validated['date'] ?? null,
            'due_date' => $validated['due_date'] ?? null,
            'payment_terms' => $validated['payment_terms'] ?? null,
            'po_number' => $validated['po_number'] ?? null,
            'subtotal' => $validated['subtotal'],
            'tax_percent' => $validated['tax_percent'],
            'discount' => $validated['discount'],
            'shipping' => $validated['shipping'],
            'total' => $validated['total'],
            'notes' => $validated['notes'] ?? null,
            'terms' => $validated['terms'] ?? null,
            'status' => $validated['status'],
        ]);

        // Attach invoice line items
        foreach ($validated['items'] as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'rate' => $item['rate'],
                'amount' => $item['amount'],
            ]);
        }

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice created successfully.');
    }
    

    public function updateStatus(Request $request, Invoice $invoice)
    {
        

        $validated = $request->validate([
            'status' => 'required|in:draft,sent,paid',
        ]);

        if ($invoice->user_id !== auth()->id()) {
            return redirect()->route('invoices.index')->withErrors('Unauthorized action.');
        }

        $invoice->status = $validated['status'];
        $invoice->save();

        return redirect()
            ->back()->with('success', 'Invoice status updated successfully.');
        
    }

    public function destroy(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            return redirect()->route('invoices.index')->withErrors('Unauthorized action.');
        }

        // Delete associated items
        $invoice->items()->delete();

        // Delete the invoice
        $invoice->delete();

        return redirect()
            ->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
    public function show(Invoice $invoice)
    {
        if ($invoice->user_id !== auth()->id()) {
            return redirect()->route('invoices.index')->withErrors('Unauthorized action.');
        }
        $invoice->load([    
            'items',
            'client',
        ]);

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
            'user' => auth()->user(),

            
        ]);
    }
        public function edit(Invoice $invoice)
        {
            $invoice->load('items'); // This adds items into the JSON sent to Vue

            $clients = Client::where('user_id', auth()->id())->get();

            return Inertia::render('Invoices/Edit', [
                'invoice' => $invoice,
                'clients' => $clients,
                'user' => Auth::user(),
            ]);
        }


    public function update(Request $request, Invoice $invoice)
{
    if ($invoice->user_id !== auth()->id()) {
        return redirect()->route('invoices.index')->withErrors('Unauthorized action.');
    }

    $validated = $request->validate([
        'from_name' => 'nullable|string|max:255',
         'client_id' => 'required|exists:clients,id',
        'invoice_number' => 'required|string|max:255',
        'date' => 'nullable|date',
        'due_date' => 'nullable|date',
        'payment_terms' => 'nullable|string|max:255',
        'po_number' => 'nullable|string|max:255',
        'subtotal' => 'required|numeric',
        'tax_percent' => 'required|numeric|min:0',
        'discount' => 'required|numeric|min:0',
        'shipping' => 'required|numeric|min:0',
        'total' => 'required|numeric',
        'notes' => 'nullable|string',
        'terms' => 'nullable|string',
        'status' => 'required|in:draft,sent,paid',
    ]);

    // Update invoice fields
    $invoice->update($validated);

    // Track incoming item IDs
    $incomingItemIds = collect($request->items)->pluck('id')->filter();

    // Delete removed items
    $invoice->items()
        ->whereNotIn('id', $incomingItemIds)
        ->delete();

    // Update or create items
    foreach ($request->items as $item) {
        if (isset($item['id'])) {
            $invoiceItem = InvoiceItem::find($item['id']);
            if ($invoiceItem && $invoiceItem->invoice_id === $invoice->id) {
                $invoiceItem->update($item);
            }
        } else {
            $invoice->items()->create($item);
        }
    }

    return redirect()
        ->route('invoices.index')
        ->with('success', 'Invoice updated successfully.');
    }

public function download(Invoice $invoice)
{
    $invoice->load('items');

    return PDF::loadView('invoices.print', [
        'invoice' => $invoice,
    ])
    ->setPaper('a4')
    ->inline("invoice_{$invoice->id}.pdf");

}


}
