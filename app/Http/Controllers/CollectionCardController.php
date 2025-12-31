<?php

namespace App\Http\Controllers;

use App\Models\Card;
use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CollectionCardController extends Controller
{
    public function store(Request $request, Collection $collection): RedirectResponse
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'card_id' => 'required|exists:cards,id',
            'quantity' => 'required|integer|min:1|max:999',
            'is_foil' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $existingCard = $collection->cards()
            ->where('card_id', $validated['card_id'])
            ->where('is_foil', $validated['is_foil'] ?? false)
            ->first();

        if ($existingCard) {
            $collection->cards()->updateExistingPivot($existingCard->id, [
                'quantity' => $existingCard->pivot->quantity + $validated['quantity'],
                'notes' => $validated['notes'] ?? $existingCard->pivot->notes,
            ]);

            $message = 'Card quantity updated in collection!';
        } else {
            $collection->cards()->attach($validated['card_id'], [
                'quantity' => $validated['quantity'],
                'is_foil' => $validated['is_foil'] ?? false,
                'notes' => $validated['notes'] ?? null,
                'added_at' => now(),
            ]);

            $message = 'Card added to collection!';
        }

        return redirect()->back()->with('success', $message);
    }

    public function update(Request $request, Collection $collection, Card $card): RedirectResponse
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:999',
            'is_foil' => 'boolean',
            'notes' => 'nullable|string',
        ]);

        $collection->cards()->updateExistingPivot($card->id, $validated);

        return redirect()->back()->with('success', 'Card updated successfully!');
    }

    public function destroy(Collection $collection, Card $card): RedirectResponse
    {
        $this->authorize('update', $collection);

        $collection->cards()->detach($card->id);

        return redirect()->back()->with('success', 'Card removed from collection!');
    }
}
