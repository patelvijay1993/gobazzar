<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GooglePlacesService
{
    private string $apiKey;
    private string $baseUrl = 'https://maps.googleapis.com/maps/api/place';

    public function __construct()
    {
        $this->apiKey = config('services.google_places.key', '');
    }

    /**
     * Search businesses by keyword + city
     * Returns array of place results
     */
    public function searchBusinesses(string $keyword, string $city, string $province = ''): array
    {
        if (!$this->apiKey) return [];

        $location = trim("$keyword in $city" . ($province ? ", $province" : '') . ', Canada');

        $results   = [];
        $pageToken = null;

        // Google Places Text Search — max 3 pages (60 results)
        for ($page = 0; $page < 3; $page++) {
            $params = [
                'query'  => $location,
                'key'    => $this->apiKey,
                'type'   => 'establishment',
            ];
            if ($pageToken) {
                $params['pagetoken'] = $pageToken;
                sleep(2); // Google requires delay before next page
            }

            $response = Http::timeout(10)->get("{$this->baseUrl}/textsearch/json", $params);

            if (!$response->ok()) break;

            $data = $response->json();

            if (($data['status'] ?? '') !== 'OK') break;

            foreach ($data['results'] ?? [] as $place) {
                $results[] = $this->formatPlace($place, $keyword, $city);
            }

            $pageToken = $data['next_page_token'] ?? null;
            if (!$pageToken) break;
        }

        return $results;
    }

    /**
     * Get detailed info (phone, website, email) for a place
     */
    public function getPlaceDetails(string $placeId): array
    {
        if (!$this->apiKey) return [];

        $response = Http::timeout(10)->get("{$this->baseUrl}/details/json", [
            'place_id' => $placeId,
            'fields'   => 'name,formatted_phone_number,website,formatted_address,rating,user_ratings_total,url',
            'key'      => $this->apiKey,
        ]);

        if (!$response->ok()) return [];

        $data   = $response->json();
        $result = $data['result'] ?? [];

        return [
            'phone'          => $result['formatted_phone_number'] ?? null,
            'website'        => $result['website'] ?? null,
            'address'        => $result['formatted_address'] ?? null,
            'rating'         => $result['rating'] ?? null,
            'review_count'   => $result['user_ratings_total'] ?? null,
            'google_maps_url'=> $result['url'] ?? null,
        ];
    }

    private function formatPlace(array $place, string $category, string $city): array
    {
        return [
            'name'           => $place['name'] ?? '',
            'category'       => $category,
            'city'           => $city,
            'address'        => $place['formatted_address'] ?? null,
            'rating'         => $place['rating'] ?? null,
            'review_count'   => $place['user_ratings_total'] ?? null,
            'google_place_id'=> $place['place_id'] ?? null,
            'google_maps_url'=> isset($place['place_id'])
                ? "https://www.google.com/maps/place/?q=place_id:{$place['place_id']}"
                : null,
        ];
    }
}
