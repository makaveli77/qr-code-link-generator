<?php

namespace App\Http\Controllers;

use App\Models\Link;
use App\Queries\UserLinksQuery;
use App\Jobs\TrackScanJob;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;

class RedirectController extends Controller
{
    public function __construct(
        protected UserLinksQuery $linkQuery
    ) {}

    public function __invoke(Request $request, string $shortCode)
    {
        // Use Cache to retrieve the original URL, avoiding a fast-path DB hit.
        // We select only needed columns to store in cache.
        $link = Cache::remember("link_redirect_{$shortCode}", now()->addMinutes(30), function () use ($shortCode) {
            return $this->linkQuery->findForRedirect($shortCode);
        });

        if (!$link) {
            abort(404, 'Link not found');
        }

        if ($link->expires_at && now()->greaterThan($link->expires_at)) {
            abort(410, 'Link has expired');
        }

        // Password protection
        if (!empty($link->password)) {
            $inputPassword = $request->input('password');
            $isApiOrAutomated = $request->expectsJson() || $request->isXmlHttpRequest() || str_contains($request->header('Accept'), 'application/json');
            if (!$inputPassword || !Hash::check($inputPassword, $link->password)) {
                if ($isApiOrAutomated) {
                    return response('Password required', 401);
                } else {
                    return response()->view('password', [
                        'short_code' => $link->short_code,
                        'original_url' => $link->original_url,
                        'error' => session('error')
                    ], 401);
                }
            }
        }

        // Fire and forget the scan tracking via RabbitMQ/Redis Queue
        TrackScanJob::dispatch(
            $link->id,
            $request->userAgent() ?? 'Unknown',
            $request->ip() ?? '127.0.0.1'
        );

        return redirect()->away($link->original_url);
    }
}