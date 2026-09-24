<?php

namespace App\Http\Middleware;

use App\Models\PageView;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TrackPageViews
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only log GET storefront requests
        if ($request->isMethod('GET') && ! $request->is('admin*') && ! $request->is('api*') && ! $request->ajax()) {
            try {
                PageView::create([
                    'ip_address' => $request->ip(),
                    'url' => $request->path(),
                    'user_agent' => substr((string) $request->userAgent(), 0, 255),
                    'viewed_date' => now()->toDateString(),
                ]);
            } catch (\Throwable $e) {
                // Ignore analytics errors
            }
        }

        return $response;
    }
}
