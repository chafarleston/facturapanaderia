<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WasteRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'product_id', 'user_id', 'fecha',
        'cantidad', 'unidad', 'motivo', 'costo_perdida', 'notas',
    ];

    protected $casts = [
        'fecha' => 'date',
        'cantidad' => 'decimal:4',
        'costo_perdida' => 'decimal:4',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
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
