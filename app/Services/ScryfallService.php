<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ScryfallService
{
    protected string $baseUrl;
    protected int $rateLimitDelay;

    public function __construct()
    {
        $this->baseUrl = config('scryfall.base_url');
        $this->rateLimitDelay = config('scryfall.rate_limit_delay');
    }

    public function searchCards(string $query, int $page = 1): array
    {
        $this->handleRateLimit();

        try {
            $searchQuery = '!"' . $query . '"';

            $response = Http::get("{$this->baseUrl}/cards/search", [
                'q' => $searchQuery,
                'page' => $page,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Scryfall API search failed', [
                'query' => $query,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Scryfall API search exception', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function getCardByName(string $name, bool $exact = true): ?array
    {
        $this->handleRateLimit();

        try {
            $endpoint = $exact
                ? "{$this->baseUrl}/cards/named?exact={$name}"
                : "{$this->baseUrl}/cards/named?fuzzy={$name}";

            $response = Http::get($endpoint);

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 404) {
                return null;
            }

            Log::error('Scryfall API get card by name failed', [
                'name' => $name,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Scryfall API get card by name exception', [
                'name' => $name,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function getCardById(string $scryfallId): ?array
    {
        $this->handleRateLimit();

        try {
            $response = Http::get("{$this->baseUrl}/cards/{$scryfallId}");

            if ($response->successful()) {
                return $response->json();
            }

            if ($response->status() === 404) {
                return null;
            }

            Log::error('Scryfall API get card by ID failed', [
                'scryfall_id' => $scryfallId,
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Scryfall API get card by ID exception', [
                'scryfall_id' => $scryfallId,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    public function autocomplete(string $query): array
    {
        $this->handleRateLimit();

        try {
            $response = Http::get("{$this->baseUrl}/cards/autocomplete", [
                'q' => $query,
            ]);

            if ($response->successful()) {
                return $response->json()['data'] ?? [];
            }

            Log::error('Scryfall API autocomplete failed', [
                'query' => $query,
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Scryfall API autocomplete exception', [
                'query' => $query,
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function bulkDataInfo(): array
    {
        $this->handleRateLimit();

        try {
            $response = Http::get("{$this->baseUrl}/bulk-data");

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Scryfall API bulk data info failed', [
                'status' => $response->status(),
            ]);

            return [];
        } catch (\Exception $e) {
            Log::error('Scryfall API bulk data info exception', [
                'error' => $e->getMessage(),
            ]);

            return [];
        }
    }

    protected function handleRateLimit(): void
    {
        usleep($this->rateLimitDelay * 1000);
    }

    public function transformCardData(array $scryfallCard): array
    {
        return [
            'scryfall_id' => $scryfallCard['id'],
            'oracle_id' => $scryfallCard['oracle_id'] ?? null,
            'name' => $scryfallCard['name'],
            'mana_cost' => $scryfallCard['mana_cost'] ?? null,
            'cmc' => $scryfallCard['cmc'] ?? 0,
            'type_line' => $scryfallCard['type_line'],
            'oracle_text' => $scryfallCard['oracle_text'] ?? null,
            'power' => $scryfallCard['power'] ?? null,
            'toughness' => $scryfallCard['toughness'] ?? null,
            'colors' => $scryfallCard['colors'] ?? null,
            'color_identity' => $scryfallCard['color_identity'] ?? null,
            'keywords' => $scryfallCard['keywords'] ?? null,
            'set_code' => $scryfallCard['set'],
            'set_name' => $scryfallCard['set_name'],
            'rarity' => $scryfallCard['rarity'],
            'image_uri_small' => $scryfallCard['image_uris']['small'] ?? null,
            'image_uri_normal' => $scryfallCard['image_uris']['normal'] ?? null,
            'image_uri_large' => $scryfallCard['image_uris']['large'] ?? null,
            'prices' => $scryfallCard['prices'] ?? null,
            'is_multi_faced' => isset($scryfallCard['card_faces']) && count($scryfallCard['card_faces']) > 1,
            'card_faces' => $scryfallCard['card_faces'] ?? null,
            'legalities' => $scryfallCard['legalities'] ?? null,
            'released_at' => $scryfallCard['released_at'] ?? null,
            'last_synced_at' => now(),
        ];
    }
}
