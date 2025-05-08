<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

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
        'customer_id', // Added for relationship
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

    // Define relationship with customer
    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    // Define relationship with items (agreement items with inventory)
    public function items()
    {
        return $this->hasMany(AgreementItem::class);
    }

    // Get items as a computed property
    public function getItemsAttribute()
    {
        return $this->agreementItems()->get();
    }

    // Helper method to get start_date in the format controller expects
    public function getStartDateAttribute($value)
    {
        return $this->attributes['startDate'] ?? Carbon::now();
    }

    // Helper method to get end_date in the format controller expects
    public function getEndDateAttribute($value)
    {
        return $this->attributes['endDate'] ?? Carbon::now();
    }

    // Helper accessor for total_price in the format controller expects
    public function getTotalPriceAttribute()
    {
        return $this->attributes['value'] ?? 0;
    }
}