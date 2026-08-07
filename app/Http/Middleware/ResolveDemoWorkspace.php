<?php

namespace App\Http\Middleware;

use App\Services\DemoWorkspaceService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveDemoWorkspace
{
    public function __construct(private readonly DemoWorkspaceService $workspaces) {}

    public function handle(Request $request, Closure $next): Response
    {
        $workspace = $this->workspaces->resolve($request->session());
        $request->attributes->set('demoWorkspace', $workspace);
        View::share('demoWorkspace', $workspace);

        return $next($request);
    }
}
