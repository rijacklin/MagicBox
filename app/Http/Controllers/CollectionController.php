<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CollectionController extends Controller
{
    public function index(): View
    {
        $ownedCollections = Auth::user()->ownedCollections()
            ->withCount('cards')
            ->latest()
            ->get();

        $sharedCollections = Auth::user()->sharedCollections()
            ->withCount('cards')
            ->latest()
            ->get();

        return view('collections.index', [
            'ownedCollections' => $ownedCollections,
            'sharedCollections' => $sharedCollections,
        ]);
    }

    public function create(): View
    {
        return view('collections.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $collection = Auth::user()->ownedCollections()->create($validated);

        return redirect()->route('collections.show', $collection)
            ->with('success', 'Collection created successfully!');
    }

    public function show(Collection $collection): View
    {
        $this->authorize('view', $collection);

        $collection->load(['cards' => function ($query) {
            $query->orderBy('name');
        }]);

        return view('collections.show', [
            'collection' => $collection,
        ]);
    }

    public function edit(Collection $collection): View
    {
        $this->authorize('update', $collection);

        return view('collections.edit', [
            'collection' => $collection,
        ]);
    }

    public function update(Request $request, Collection $collection): RedirectResponse
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'boolean',
        ]);

        $collection->update($validated);

        return redirect()->route('collections.show', $collection)
            ->with('success', 'Collection updated successfully!');
    }

    public function destroy(Collection $collection): RedirectResponse
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return redirect()->route('collections.index')
            ->with('success', 'Collection deleted successfully!');
    }
}
