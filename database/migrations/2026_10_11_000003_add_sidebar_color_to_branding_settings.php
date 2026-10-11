<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->string('sidebar_color', 7)->nullable()->after('accent_color');
        });

        DB::table('branding_settings')
            ->whereRaw('LOWER(accent_color) = ?', ['#3f6b30'])
            ->update(['accent_color' => null]);

        Cache::forget('branding_settings:'.(tenant()?->getTenantKey() ?? 'central'));
    }

    public function down(): void
    {
        Schema::table('branding_settings', function (Blueprint $table) {
            $table->dropColumn('sidebar_color');
        });
    }
};