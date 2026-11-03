<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteChatConversation extends Model
{
    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'visitor_name',
        'visitor_user_id',
        'assigned_staff_id',
        'status',
        'last_message_at',
    ];

    protected $casts = ['last_message_at' => 'datetime'];

    public function messages(): HasMany
    {
        return $this->hasMany(SiteChatMessage::class, 'conversation_id');
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'visitor_user_id');
    }

    public function assignedStaff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_staff_id');
    }
}
