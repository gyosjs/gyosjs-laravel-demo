<?php

namespace App\Http\Controllers;

use App\Models\DemoWorkspace;
use App\Services\DemoWorkspaceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DemoController extends Controller
{
    public function reset(Request $request, DemoWorkspaceService $workspaces): RedirectResponse
    {
        /** @var DemoWorkspace $workspace */
        $workspace = $request->attributes->get('demoWorkspace');
        $workspaces->reset($workspace);

        return redirect()->route('products.index')->with('success', 'Demo workspace reset to its original 36 products.');
    }
}
