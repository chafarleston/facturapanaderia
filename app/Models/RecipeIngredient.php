<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecipeIngredient extends Model
{
    use HasFactory;

    protected $fillable = [
        'recipe_id', 'product_id', 'cantidad', 'unidad',
        'merma_porcentaje', 'costo_unitario',
    ];

    protected $casts = [
        'cantidad' => 'decimal:4',
        'merma_porcentaje' => 'decimal:2',
        'costo_unitario' => 'decimal:4',
    ];

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
