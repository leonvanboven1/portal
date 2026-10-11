<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class PlotListingSetting extends Model
{
    public const DEFAULT_MAX_IMAGE_SIZE_MB = 20;

    protected $fillable = [
        'max_image_size_mb',
    ];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget(self::cacheKey()));
    }

    public static function current(): self
    {
        if (! Cache::has(self::cacheKey()) && ! Schema::hasTable('plot_listing_settings')) {
            return new self(['max_image_size_mb' => self::DEFAULT_MAX_IMAGE_SIZE_MB]);
        }

        return Cache::rememberForever(self::cacheKey(), fn () => self::first() ?? new self([
            'max_image_size_mb' => self::DEFAULT_MAX_IMAGE_SIZE_MB,
        ]));
    }

    public function max_image_kb(): int
    {
        return max(1, (int) $this->max_image_size_mb) * 1024;
    }

    private static function cacheKey(): string
    {
        return 'plot_listing_settings:'.(tenant()?->getTenantKey() ?? 'central');
    }
}
