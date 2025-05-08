<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
        'inventory_id', // Added for relationship with inventory
        'name',
        'quantity',
        'rate',
        'total'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'rate' => 'float',
        'total' => 'float'
    ];

    public function agreement()
    {
        return $this->belongsTo(Agreement::class);
    }
    
    // Add relationship to Inventory
    public function inventory()
    {
        return $this->belongsTo(Inventory::class, 'inventory_id');
    }
}