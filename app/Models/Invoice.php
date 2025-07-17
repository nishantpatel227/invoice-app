<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'from_name',
        'to_name',
        'client_id',
        'invoice_number',
        'date',
        'due_date',
        'payment_terms',
        'po_number',
        'subtotal',
        'tax_percent',
        'discount',
        'shipping',
        'amount_paid',
        'total',
        'currency',
        'notes',
        'terms',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
