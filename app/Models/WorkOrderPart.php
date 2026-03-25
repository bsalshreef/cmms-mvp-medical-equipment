<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkOrderPart extends Model
{
    use HasFactory;

    protected $fillable = [
        'work_order_id',
        'spare_part_id',
        'quantity_used',
        'unit_cost',
        'notes',
        'issued_by_user_id',
    ];

    protected $casts = [
        'unit_cost' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by_user_id');
    }
}
