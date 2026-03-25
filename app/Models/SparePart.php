<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SparePart extends Model
{
    use HasFactory;

    protected $fillable = [
        'part_code',
        'name',
        'description',
        'unit',
        'unit_cost',
        'current_quantity',
        'minimum_quantity',
        'vendor_id',
        'location',
        'notes',
    ];

    protected $casts = [
        'unit_cost'        => 'decimal:2',
        'current_quantity' => 'integer',
        'minimum_quantity' => 'integer',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function workOrderParts()
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    /** True when stock is at or below the minimum threshold */
    public function isLowStock(): bool
    {
        return $this->current_quantity <= $this->minimum_quantity;
    }
}
