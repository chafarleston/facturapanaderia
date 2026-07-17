<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'nombre', 'precio_envio', 'tiempo_estimado_min', 'activa',
    ];

    protected $casts = [
        'precio_envio' => 'decimal:2',
        'activa' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function deliveries()
    {
        return $this->hasMany(Delivery::class);
    }
}
