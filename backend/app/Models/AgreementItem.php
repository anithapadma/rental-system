<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgreementItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'agreement_id',
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
}