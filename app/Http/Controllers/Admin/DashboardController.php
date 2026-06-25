<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\Admin\ContentRegistry;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $cards = collect(ContentRegistry::all())->map(function (array $definition, string $type) {
            return [
                'type' => $type,
                'label' => $definition['label'],
                'count' => $definition['model']::query()->count(),
            ];
        });

        return view('admin.dashboard', compact('cards'));
    }
}
