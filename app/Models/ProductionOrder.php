<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'recipe_id', 'product_id', 'user_id',
        'fecha_produccion', 'cantidad_planificada', 'cantidad_producida',
        'unidad', 'estado', 'costo_total', 'notas',
    ];

    protected $casts = [
        'fecha_produccion' => 'date',
        'cantidad_planificada' => 'decimal:4',
        'cantidad_producida' => 'decimal:4',
        'costo_total' => 'decimal:4',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
