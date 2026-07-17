<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'invoice_id', 'delivery_zone_id', 'delivery_person_id',
        'direccion', 'referencia', 'telefono_contacto', 'costo_envio',
        'estado', 'fecha_entrega', 'notas',
    ];

    protected $casts = [
        'costo_envio' => 'decimal:2',
        'fecha_entrega' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function deliveryZone()
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function deliveryPerson()
    {
        return $this->belongsTo(DeliveryPerson::class);
    }
}
