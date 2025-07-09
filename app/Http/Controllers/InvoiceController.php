<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index()
    {
        $invoices = Invoice::with('items')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return Inertia::render('Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }

    public function create()
    {
        return Inertia::render('Invoices/Create', [
            'user' => \Illuminate\Support\Facades\Auth::user(),
            'nextInvoiceNumber' => 'INV-' . str_pad(Invoice::max('id') + 1, 5, '0', STR_PAD_LEFT),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'from_name' => 'nullable|string|max:255',
            'to_name' => 'nullable|string|max:255',
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
            'to_name' => $validated['to_name'] ?? '',
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

        return Inertia::render('Invoices/Show', [
            'invoice' => $invoice->load('items'),
        ]);
    }
    public function edit(Invoice $invoice)
    {
        $invoice->load('items');
        return Inertia::render('Invoices/Edit', [
            'invoice' => $invoice,
            'user' => auth()->user(),
        ]);
    }


}
