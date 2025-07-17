<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 14px; line-height: 1.6; margin: 40px; }
        h2 { margin-bottom: 5px; }
        .header { margin-bottom: 30px; }
        .addresses { display: flex; justify-content: space-between; }
        .address-block { width: 48%; }
        .meta { margin-top: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #f9f9f9; }
        .totals { margin-top: 20px; width: 100%; border: none; }
        .totals td { text-align: right; padding: 6px 8px; }
        .totals tr td:first-child { text-align: left; }
        .notes, .terms { margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Invoice #{{ $invoice->invoice_number }}</h2>
        <div class="meta">
            <p><strong>Date:</strong> {{ $invoice->date }}</p>
            <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
            <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
        </div>
    </div>

    <div class="addresses">
        <div class="address-block">
            <strong>From:</strong><br>
            {{ $invoice->from_name }}<br>
            {{-- Add more fields if you store company/address for sender --}}
        </div>
        <div class="address-block">
            <strong>To:</strong><br>
            {{ $invoice->client->name ?? $invoice->to_name }}<br>
            @if (!empty($invoice->client?->company_name))
                {{ $invoice->client->company_name }}<br>
            @endif
            @if (!empty($invoice->client?->email))
                {{ $invoice->client->email }}<br>
            @endif
            {{-- You can include client address here if available --}}
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>Qty</th>
                <th>Rate</th>
                <th>Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>${{ number_format($item->rate, 2) }}</td>
                    <td>${{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td>Subtotal:</td>
            <td>${{ number_format($invoice->subtotal, 2) }}</td>
        </tr>
        <tr>
            <td>Tax ({{ $invoice->tax_percent }}%):</td>
            <td>${{ number_format($invoice->subtotal * $invoice->tax_percent / 100, 2) }}</td>
        </tr>
        <tr>
            <td>Discount ({{ $invoice->discount }}%):</td>
            <td>-${{ number_format($invoice->subtotal * $invoice->discount / 100, 2) }}</td>
        </tr>
        <tr>
            <td>Shipping:</td>
            <td>${{ number_format($invoice->shipping, 2) }}</td>
        </tr>
        <tr>
            <td><strong>Total:</strong></td>
            <td><strong>${{ number_format($invoice->total, 2) }}</strong></td>
        </tr>
    </table>

    @if (!empty($invoice->notes))
        <div class="notes">
            <strong>Notes:</strong>
            <p>{{ $invoice->notes }}</p>
        </div>
    @endif

    @if (!empty($invoice->terms))
        <div class="terms">
            <strong>Terms:</strong>
            <p>{{ $invoice->terms }}</p>
        </div>
    @endif
</body>
</html>
