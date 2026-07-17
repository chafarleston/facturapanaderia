<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduledOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheduled_order_id', 'product_id', 'descripcion_personalizada',
        'cantidad', 'precio_unitario', 'subtotal', 'notas',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'precio_unitario' => 'decimal:4',
        'subtotal' => 'decimal:2',
    ];

    public function scheduledOrder()
    {
        return $this->belongsTo(ScheduledOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
