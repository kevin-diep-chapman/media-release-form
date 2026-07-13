<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = config('security.headers', []);

        if ($headers['x_frame_options'] ?? false) {
            $response->headers->set('X-Frame-Options', $headers['x_frame_options']);
        }

        if ($headers['x_content_type_options'] ?? false) {
            $response->headers->set('X-Content-Type-Options', $headers['x_content_type_options']);
        }

        if ($headers['referrer_policy'] ?? false) {
            $response->headers->set('Referrer-Policy', $headers['referrer_policy']);
        }

        if ($headers['permissions_policy'] ?? false) {
            $response->headers->set('Permissions-Policy', $headers['permissions_policy']);
        }

        if (array_key_exists('x_xss_protection', $headers)) {
            $response->headers->set('X-XSS-Protection', (string) $headers['x_xss_protection']);
        }

        $hsts = config('security.hsts', []);

        if (
            ($hsts['enabled'] ?? false)
            && $request->secure()
        ) {
            $maxAge = (int) ($hsts['max_age'] ?? 31536000);
            $value = 'max-age='.$maxAge;

            if ($hsts['include_subdomains'] ?? false) {
                $value .= '; includeSubDomains';
            }

            $response->headers->set('Strict-Transport-Security', $value);
        }

        return $response;
    }
}
