<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Posts to the GoBazaar Facebook Page via the Graph API.
 *
 * Needs a long-lived PAGE access token (not a personal user token) — generate one via
 * Meta for Developers → your app → Graph API Explorer, select the Page, grant
 * pages_manage_posts + pages_read_engagement, then exchange the short-lived token for a
 * long-lived one. See FACEBOOK_SETUP.md for the full step-by-step.
 */
class FacebookPageService
{
    private ?string $pageId;
    private ?string $accessToken;
    private string $baseUrl = 'https://graph.facebook.com/v21.0';

    public function __construct()
    {
        $this->pageId      = config('services.facebook_page.page_id');
        $this->accessToken = config('services.facebook_page.access_token');
    }

    public function isConfigured(): bool
    {
        return (bool) ($this->pageId && $this->accessToken);
    }

    /**
     * Post a message (with an optional link and/or photo) to the Page's feed.
     * Returns the new post's id on success, or null on failure (never throws —
     * a Facebook outage should never break the site's own posting flow).
     */
    public function post(string $message, ?string $link = null, ?string $imageUrl = null): ?string
    {
        if (!$this->isConfigured()) {
            Log::info('FacebookPageService: skipped post — FACEBOOK_PAGE_ID/FACEBOOK_PAGE_ACCESS_TOKEN not configured.');
            return null;
        }

        try {
            // A photo post ignores the `link` param (FB shows the image, not a link preview),
            // so the link is appended to the message text instead for a photo post.
            if ($imageUrl) {
                $response = Http::timeout(15)->post("{$this->baseUrl}/{$this->pageId}/photos", [
                    'url'          => $imageUrl,
                    'caption'      => $link ? "{$message}\n\n{$link}" : $message,
                    'access_token' => $this->accessToken,
                ]);
            } else {
                $params = [
                    'message'      => $message,
                    'access_token' => $this->accessToken,
                ];
                if ($link) $params['link'] = $link;

                $response = Http::timeout(15)->post("{$this->baseUrl}/{$this->pageId}/feed", $params);
            }

            if (!$response->ok()) {
                Log::warning('FacebookPageService: post failed', ['response' => $response->json()]);
                return null;
            }

            $data = $response->json();
            // /photos returns {id, post_id}; /feed returns {id}. The feed-visible post id is
            // post_id when present, otherwise id.
            return $data['post_id'] ?? $data['id'] ?? null;
        } catch (\Throwable $e) {
            Log::error('FacebookPageService: exception while posting', ['error' => $e->getMessage()]);
            return null;
        }
    }
}
