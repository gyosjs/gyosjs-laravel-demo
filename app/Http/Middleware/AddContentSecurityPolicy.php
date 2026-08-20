<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class AddContentSecurityPolicy
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(random_bytes(18));

        Vite::useCspNonce($nonce);
        View::share('cspNonce', $nonce);

        $response = $next($request);
        $response->headers->set('Content-Security-Policy', $this->policy($nonce));

        return $response;
    }

    private function policy(string $nonce): string
    {
        $scriptSources = ["'self'", "'nonce-$nonce'"];
        $styleSources = ["'self'", "'nonce-$nonce'"];
        $connectSources = ["'self'"];

        if (app()->isLocal() && is_file(public_path('hot'))) {
            $viteOrigin = $this->viteOrigin();

            if ($viteOrigin !== null) {
                $scriptSources[] = $viteOrigin;
                $styleSources[] = $viteOrigin;
                // Vite HMR injects style tags without Laravel's server nonce.
                $styleSources[] = "'unsafe-inline'";
                $connectSources[] = $viteOrigin;
                $connectSources[] = preg_replace('/^http/', 'ws', $viteOrigin);
            }
        }

        return implode('; ', [
            "default-src 'self'",
            'script-src '.implode(' ', array_unique($scriptSources)),
            'style-src '.implode(' ', array_unique($styleSources)),
            'connect-src '.implode(' ', array_unique($connectSources)),
            "img-src 'self' data:",
            "font-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ]).';';
    }

    private function viteOrigin(): ?string
    {
        $url = trim((string) file_get_contents(public_path('hot')));
        $parts = parse_url($url);

        if (! isset($parts['scheme'], $parts['host'])) {
            return null;
        }

        return $parts['scheme'].'://'.$parts['host'].(isset($parts['port']) ? ':'.$parts['port'] : '');
    }
}
