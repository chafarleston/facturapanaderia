<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'nombre', 'descripcion', 'result_product_id',
        'cantidad_producida', 'unidad', 'tiempo_preparacion_min',
        'instrucciones', 'costo_estimado', 'activa',
    ];

    protected $casts = [
        'cantidad_producida' => 'decimal:4',
        'costo_estimado' => 'decimal:4',
        'activa' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function resultProduct()
    {
        return $this->belongsTo(Product::class, 'result_product_id');
    }

    public function ingredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }
}
