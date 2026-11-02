<?php

namespace App\Http\Controllers;

use App\Models\BrandingSetting;
use App\Models\SiteContent;
use App\Models\SiteRuleCategory;
use App\Services\Site\MinecraftServerStatus;

class SiteController extends Controller
{
    public function index(MinecraftServerStatus $serverStatus)
    {
        return view('site.home', [
            'content' => SiteContent::current(),
            'branding' => BrandingSetting::current(),
            'serverStatus' => $serverStatus->get(),
        ]);
    }

    public function rules()
    {
        return view('site.rules', [
            'categories' => SiteRuleCategory::with('rules')->orderBy('sort_order')->orderBy('id')->get(),
            'content' => SiteContent::current(),
            'branding' => BrandingSetting::current(),
        ]);
    }
}
