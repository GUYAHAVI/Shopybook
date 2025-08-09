<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AIBusinessAdvice extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_id',
        'advice_type',
        'priority',
        'title',
        'description',
        'action_items',
        'expected_impact',
        'advice_data',
        'is_read'
    ];

    protected $casts = [
        'action_items' => 'array',
        'advice_data' => 'array',
        'is_read' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the business that owns the advice
     */
    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    /**
     * Scope for unread advice
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope for high priority advice
     */
    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'critical']);
    }

    /**
     * Get priority color for UI
     */
    public function getPriorityColorAttribute()
    {
        return [
            'low' => 'green',
            'medium' => 'yellow',
            'high' => 'orange',
            'critical' => 'red'
        ][$this->priority] ?? 'blue';
    }

    /**
     * Get priority icon for UI
     */
    public function getPriorityIconAttribute()
    {
        return [
            'low' => 'info-circle',
            'medium' => 'exclamation-triangle',
            'high' => 'exclamation-circle',
            'critical' => 'times-circle'
        ][$this->priority] ?? 'info-circle';
    }
}
