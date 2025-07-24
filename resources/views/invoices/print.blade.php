<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: sans-serif;
            color: #333;
            font-size: 14px;
            line-height: 1.6;
            margin: 40px;
        }
        h2 {
            margin-bottom: 5px;
        }
        .header, .addresses, .billing-shipping, .notes-terms {
            margin-bottom: 30px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            gap: 30px;
        }
        .col {
            width: 48%;
        }
        .meta p, .address-block p {
            margin: 0 0 4px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f9f9f9;
        }
        .totals {
            margin-top: 20px;
            width: 100%;
            border: none;
        }
        .totals td {
            text-align: right;
            padding: 6px 8px;
        }
        .totals tr td:first-child {
            text-align: left;
        }
        .notes-terms .col {
            vertical-align: top;
        }
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

    <div class="addresses row">
        <div class="address-block col">
            <strong>From:</strong><br>
            <p>{{ $invoice->from_name }}</p>
            {{-- Add your own address or company details here --}}
        </div>
        <div class="address-block col">
            <strong>To:</strong><br>
            <p>{{ $invoice->client->name ?? $invoice->to_name }}</p>
            @if (!empty($invoice->client?->company_name))
                <p>{{ $invoice->client->company_name }}</p>
            @endif
            @if (!empty($invoice->client?->email))
                <p>{{ $invoice->client->email }}</p>
            @endif
            @if (!empty($invoice->client?->phone_personal))
                <p>{{ $invoice->client->phone_personal }}</p>
            @endif
        </div>
    </div>

    @if (!empty($invoice->client?->billing_address) || !empty($invoice->client?->shipping_address))
        <div class="billing-shipping row">
            <div class="col">
                <strong>Billing Address:</strong><br>
                <p>{{ $invoice->client?->billing_address ?? 'N/A' }}</p>
            </div>
            <div class="col">
                <strong>Shipping Address:</strong><br>
                <p>{{ $invoice->client?->shipping_address ?? 'N/A' }}</p>
            </div>
        </div>
    @endif

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

    @if (!empty($invoice->notes) || !empty($invoice->terms))
        <div class="notes-terms row">
            @if (!empty($invoice->notes))
                <div class="col">
                    <strong>Notes:</strong>
                    <p>{{ $invoice->notes }}</p>
                </div>
            @endif
            @if (!empty($invoice->terms))
                <div class="col">
                    <strong>Terms:</strong>
                    <p>{{ $invoice->terms }}</p>
                </div>
            @endif
        </div>
    @endif
</body>
</html>
