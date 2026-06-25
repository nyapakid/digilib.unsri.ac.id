<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::active()->where('slug', $slug)->firstOrFail();

        return view('pages.show', [
            'page' => $page,
            'site' => SiteSetting::current(),
            'menus' => MenuItem::active()->ordered()->get(),
        ]);
    }
}
