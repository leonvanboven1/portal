<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EducationEnrollment extends Model
{
    protected $fillable = [
        'user_id',
        'education_course_id',
        'payer_bank_account_uuid',
        'payout_bank_account_uuid',
        'amount_paid',
        'started_at',
        'completes_at',
        'completed_at',
        'status',
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'started_at' => 'datetime',
        'completes_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(EducationCourse::class, 'education_course_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}