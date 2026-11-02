<?php

namespace App\Http\Controllers;

use App\Models\SiteContent;

class SiteController extends Controller
{
    public function index()
    {
        return view('site.home', [
            'content' => SiteContent::current(),
        ]);
    }
}
