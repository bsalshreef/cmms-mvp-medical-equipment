<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'device_id',
        'service_category_id',
        'priority',
        'description',
        'target_start_datetime',
        'workflow_status',
        'resolution_status',
        'resolution_notes',
        'created_by_user_id',
        'assigned_to_user_id',
        'closed_by_user_id',
        'closed_at',
    ];

    protected $casts = [
        'target_start_datetime' => 'datetime',
        'closed_at'             => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class);
    }

    /** The user who created / requested this work order */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    /** The technician assigned to this work order */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    /** The user who closed this work order */
    public function closer()
    {
        return $this->belongsTo(User::class, 'closed_by_user_id');
    }

    /** File attachments */
    public function attachments()
    {
        return $this->hasMany(WorkOrderAttachment::class);
    }

    /** Spare parts consumed */
    public function parts()
    {
        return $this->hasMany(WorkOrderPart::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    /** Exclude terminal statuses */
    public function scopeOpen($query)
    {
        return $query->whereNotIn('workflow_status', ['COMPLETED', 'CLOSED', 'CANCELLED']);
    }
}
