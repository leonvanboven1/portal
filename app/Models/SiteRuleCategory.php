<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SiteRuleCategory extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function rules(): HasMany
    {
        return $this->hasMany(SiteRule::class)->orderBy('sort_order')->orderBy('id');
    }
}
