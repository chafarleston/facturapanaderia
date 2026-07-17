<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryPerson extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'nombre', 'telefono', 'vehiculo', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
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
