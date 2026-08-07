<?php

namespace App\Http\Controllers;

use App\Models\DemoWorkspace;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        /** @var DemoWorkspace $workspace */
        $workspace = $request->attributes->get('demoWorkspace');
        $products = $workspace->products();

        return view('dashboard.index', [
            'title' => 'Operations overview',
            'metrics' => [
                'total' => (clone $products)->count(),
                'active' => (clone $products)->where('status', 'active')->count(),
                'lowStock' => (clone $products)->where('stock', '<', 12)->count(),
                'inventoryValue' => (clone $products)->selectRaw('SUM(price * stock) as value')->value('value') ?? 0,
            ],
            'recentProducts' => $workspace->products()->latest('updated_at')->limit(5)->get(),
        ]);
    }
}
