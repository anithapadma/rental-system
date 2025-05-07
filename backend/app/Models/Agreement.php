<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer',
        'customerEmail',
        'customerPhone',
        'startDate',
        'endDate',
        'value',
        'status',
        'paymentMethod',
        'paymentStatus',
        'notes',
        'createdDate',
        'updatedDate'
    ];

    protected $casts = [
        'startDate' => 'date',
        'endDate' => 'date',
        'value' => 'float',
        'createdDate' => 'date',
        'updatedDate' => 'date',
    ];

    protected $appends = ['items'];

    // Define relationship with agreement items
    public function agreementItems()
    {
        return $this->hasMany(AgreementItem::class);
    }

    // Get items as a computed property
    public function getItemsAttribute()
    {
        return $this->agreementItems()->get();
    }
}