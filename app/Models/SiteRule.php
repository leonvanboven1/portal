<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiteRule extends Model
{
    protected $fillable = ['site_rule_category_id', 'title', 'body', 'sort_order'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SiteRuleCategory::class, 'site_rule_category_id');
    }
}
