<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Services\CardCacheService;
use App\Services\ScryfallService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CardController extends Controller
{
    public function __construct(
        protected CardCacheService $cardCacheService,
        protected ScryfallService $scryfallService
    ) {}

    public function index(Request $request): View
    {
        $query = $request->input('q');
        $cards = collect();
        $hasMore = false;
        $totalCards = 0;

        if ($query) {
            $searchResults = $this->cardCacheService->searchCards($query);
            $cards = collect($searchResults['data'] ?? []);
            $hasMore = $searchResults['has_more'] ?? false;
            $totalCards = $searchResults['total_cards'] ?? 0;
        }

        return view('cards.index', [
            'cards' => $cards,
            'query' => $query,
            'hasMore' => $hasMore,
            'totalCards' => $totalCards,
        ]);
    }

    public function show(Card $card): View
    {
        if ($card->needsSync()) {
            $card = $this->cardCacheService->syncCard($card);
        }

        return view('cards.show', [
            'card' => $card,
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q');
        $page = $request->input('page', 1);

        if (!$query) {
            return response()->json([
                'data' => [],
                'has_more' => false,
                'total_cards' => 0,
            ]);
        }

        $results = $this->cardCacheService->searchCards($query, $page);

        return response()->json([
            'data' => $results['data'] ?? [],
            'has_more' => $results['has_more'] ?? false,
            'total_cards' => $results['total_cards'] ?? 0,
        ]);
    }

    public function autocomplete(Request $request): JsonResponse
    {
        $query = $request->input('q');

        if (!$query || strlen($query) < 2) {
            return response()->json([]);
        }

        $suggestions = $this->scryfallService->autocomplete($query);

        return response()->json($suggestions);
    }
}
