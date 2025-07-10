<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: sans-serif; color: #333; font-size: 14px; }
        .header { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .totals { margin-top: 20px; width: 100%; }
        .totals td { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Invoice #{{ $invoice->invoice_number }}</h2>
        <p><strong>From:</strong> {{ $invoice->from_name }}</p>
        <p><strong>To:</strong> {{ $invoice->to_name }}</p>
        <p><strong>Date:</strong> {{ $invoice->date }}</p>
        <p><strong>Due Date:</strong> {{ $invoice->due_date }}</p>
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
                    <td>{{ number_format($item->rate, 2) }}</td>
                    <td>{{ number_format($item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr><td>Subtotal:</td><td>{{ number_format($invoice->subtotal, 2) }}</td></tr>
        <tr><td>Tax ({{ $invoice->tax_percent }}%):</td><td>{{ number_format($invoice->subtotal * $invoice->tax_percent / 100, 2) }}</td></tr>
        <tr><td>Discount ({{ $invoice->discount }}%):</td><td>{{ number_format($invoice->subtotal * $invoice->discount / 100, 2) }}</td></tr>
        <tr><td>Shipping:</td><td>{{ number_format($invoice->shipping, 2) }}</td></tr>
        <tr><td><strong>Total:</strong></td><td><strong>{{ number_format($invoice->total, 2) }}</strong></td></tr>
    </table>

    <p><strong>Notes:</strong> {{ $invoice->notes }}</p>
    <p><strong>Terms:</strong> {{ $invoice->terms }}</p>
</body>
</html>
