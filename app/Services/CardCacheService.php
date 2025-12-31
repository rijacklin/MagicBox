<?php

namespace App\Services;

use App\Models\Card;
use Illuminate\Support\Facades\Log;

class CardCacheService
{
    protected ScryfallService $scryfallService;
    protected int $cacheDurationDays;

    public function __construct(ScryfallService $scryfallService)
    {
        $this->scryfallService = $scryfallService;
        $this->cacheDurationDays = config('scryfall.cache_duration_days');
    }

    public function findOrFetchCard(string $name, ?string $setCode = null): ?Card
    {
        $query = Card::where('name', $name);

        if ($setCode) {
            $query->where('set_code', $setCode);
        }

        $card = $query->first();

        if ($card && !$this->shouldSync($card)) {
            return $card;
        }

        $scryfallData = $this->scryfallService->getCardByName($name);

        if (!$scryfallData) {
            return $card;
        }

        if ($card) {
            $card->update($this->scryfallService->transformCardData($scryfallData));
            return $card->fresh();
        }

        return Card::create($this->scryfallService->transformCardData($scryfallData));
    }

    public function findOrFetchCardById(string $scryfallId): ?Card
    {
        $card = Card::where('scryfall_id', $scryfallId)->first();

        if ($card && !$this->shouldSync($card)) {
            return $card;
        }

        $scryfallData = $this->scryfallService->getCardById($scryfallId);

        if (!$scryfallData) {
            return $card;
        }

        if ($card) {
            $card->update($this->scryfallService->transformCardData($scryfallData));
            return $card->fresh();
        }

        return Card::create($this->scryfallService->transformCardData($scryfallData));
    }

    public function syncCard(Card $card): Card
    {
        $scryfallData = $this->scryfallService->getCardById($card->scryfall_id);

        if ($scryfallData) {
            $card->update($this->scryfallService->transformCardData($scryfallData));
        }

        return $card->fresh();
    }

    public function shouldSync(Card $card): bool
    {
        if (!$card->last_synced_at) {
            return true;
        }

        return $card->last_synced_at->diffInDays(now()) >= $this->cacheDurationDays;
    }

    public function pruneOldCache(int $daysOld = 30): int
    {
        $cutoffDate = now()->subDays($daysOld);

        $deletedCount = Card::where('last_synced_at', '<', $cutoffDate)
            ->whereDoesntHave('collections')
            ->delete();

        Log::info('Pruned old card cache', [
            'deleted_count' => $deletedCount,
            'days_old' => $daysOld,
        ]);

        return $deletedCount;
    }

    public function searchCards(string $query, int $page = 1): array
    {
        $results = $this->scryfallService->searchCards($query, $page);

        if (empty($results['data'])) {
            return [];
        }

        $cards = [];

        foreach ($results['data'] as $scryfallCard) {
            $existingCard = Card::where('scryfall_id', $scryfallCard['id'])->first();

            if ($existingCard && !$this->shouldSync($existingCard)) {
                $cards[] = $existingCard;
                continue;
            }

            $cardData = $this->scryfallService->transformCardData($scryfallCard);

            if ($existingCard) {
                $existingCard->update($cardData);
                $cards[] = $existingCard->fresh();
            } else {
                $cards[] = Card::create($cardData);
            }
        }

        return [
            'data' => $cards,
            'has_more' => $results['has_more'] ?? false,
            'total_cards' => $results['total_cards'] ?? count($cards),
        ];
    }
}
