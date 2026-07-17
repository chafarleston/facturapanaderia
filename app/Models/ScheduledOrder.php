<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'customer_id', 'user_id', 'order_number',
        'fecha_pedido', 'fecha_entrega', 'hora_entrega',
        'estado', 'subtotal', 'igv', 'total', 'anticipo', 'saldo',
        'notas', 'descripcion', 'telefono_contacto', 'para_delivery',
    ];

    protected $casts = [
        'fecha_pedido' => 'date',
        'fecha_entrega' => 'date',
        'subtotal' => 'decimal:2',
        'igv' => 'decimal:2',
        'total' => 'decimal:2',
        'anticipo' => 'decimal:2',
        'saldo' => 'decimal:2',
        'para_delivery' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(ScheduledOrderItem::class);
    }
}
