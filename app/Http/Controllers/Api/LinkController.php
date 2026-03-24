<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Http\Requests\StoreLinkRequest;
use App\DTOs\CreateLinkDTO;
use App\Services\LinkService;
use App\Services\QrCodeGenerationService;
use App\Http\Resources\LinkResource;
use App\Models\Link;
use App\Queries\UserLinksQuery;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class LinkController extends Controller
{
    use AuthorizesRequests;
    public function __construct(
        protected LinkService $linkService,
        protected QrCodeGenerationService $qrService,
        protected UserLinksQuery $linkQuery
    ) {}

    // Update QR branding for a link
    public function updateQrBranding(\App\Http\Requests\UpdateQrBrandingRequest $request, Link $link): LinkResource
    {
        $this->authorize('update', $link);
        $link = $this->linkService->updateQrBranding(
            $link, 
            $request->validated(), 
            $request->file('logo')
        );
        return new LinkResource($link);
    }

    public function store(StoreLinkRequest $request): LinkResource
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('qrcodes/logos', 'public');
        }
        $data['user_id'] = $request->user()?->id;
        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        // Handle custom alias and expiration
        if (!empty($data['custom_alias'])) {
            $data['short_code'] = $data['custom_alias'];
        }
        if (!empty($data['expires_at'])) {
            $data['expires_at'] = $data['expires_at'];
        }
        $dto = CreateLinkDTO::fromArray($data);
        $link = $this->linkService->createLink($dto);
        return new LinkResource($link);
    }

    public function show(Link $link): LinkResource
    {
        $link->load('qrCode');
        return new LinkResource($link);
    }

    public function downloadQr(string $shortCode)
    {
        $link = $this->linkQuery->findWithQrCode($shortCode);
        
        $cacheKey = "qrcode_svg_{$link->short_code}_" . md5(serialize($link->qrCode));
        $qrCodeSvg = Cache::remember($cacheKey, now()->addDays(7), function () use ($link) {
            return $this->qrService->generateQrCode(url('/' . $link->short_code), $link->qrCode);
        });

        // Return as downloadable file
        return response($qrCodeSvg)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="qrcode-' . $link->short_code . '.svg"');
    }

        // List all links for the authenticated user
        public function index(Request $request): JsonResponse
        {
            $links = $this->linkQuery->getForUser($request->user());
            return response()->json([
                'data' => LinkResource::collection($links),
                'meta' => [
                    'current_page' => $links->currentPage(),
                    'last_page' => $links->lastPage(),
                    'per_page' => $links->perPage(),
                    'total' => $links->total(),
                ]
            ]);
        }

    // Update a link
    public function update(\App\Http\Requests\UpdateLinkRequest $request, Link $link): LinkResource
    {
        $this->authorize('update', $link);
        $link = $this->linkService->updateLink($link, $request->validated());
        return new LinkResource($link);
    }

        // Delete a link
        public function destroy(Request $request, Link $link): JsonResponse
        {
            $this->authorize('delete', $link);
            $link->delete();
            return response()->json(['message' => 'Link deleted successfully.']);
    }
}
