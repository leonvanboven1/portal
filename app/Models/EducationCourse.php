<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EducationCourse extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'duration_hours',
        'payout_bank_account_uuid',
        'qualifies_for_company',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'duration_hours' => 'integer',
        'qualifies_for_company' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'education_course_prerequisites', 'course_id', 'prerequisite_id');
    }

    public function requiredBy(): BelongsToMany
    {
        return $this->belongsToMany(self::class, 'education_course_prerequisites', 'prerequisite_id', 'course_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(EducationEnrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}