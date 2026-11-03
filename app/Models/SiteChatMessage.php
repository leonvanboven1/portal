<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteChatMessage extends Model
{
    protected $fillable = [
        'conversation_id',
        'sender_type',
        'sender_user_id',
        'sender_name',
        'body',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(SiteChatConversation::class, 'conversation_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_user_id');
    }
}
