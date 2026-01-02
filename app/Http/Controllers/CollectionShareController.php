<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Models\CollectionShare;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CollectionShareController extends Controller
{
    public function store(Request $request, Collection $collection): RedirectResponse
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'permission_level' => 'required|in:view,edit',
        ]);

        $recipient = User::where('email', $validated['email'])->first();

        if ($recipient->id === Auth::id()) {
            return back()->withErrors(['email' => 'You cannot share a collection with yourself.']);
        }

        $existingShare = $collection->shares()
            ->where('user_id', $recipient->id)
            ->exists();

        if ($existingShare) {
            return back()->withErrors(['email' => "This collection is already shared with {$recipient->name}."]);
        }

        CollectionShare::create([
            'collection_id' => $collection->id,
            'user_id' => $recipient->id,
            'shared_by_user_id' => Auth::id(),
            'permission_level' => $validated['permission_level'],
        ]);

        return back()->with('success', "Collection shared successfully with {$recipient->name}! They will need to accept the invitation.");
    }

    public function accept(CollectionShare $share): RedirectResponse
    {
        $this->authorize('accept', $share);

        $share->accept();

        return redirect()->route('collections.index')
            ->with('success', "You now have access to '{$share->collection->name}'!");
    }

    public function reject(CollectionShare $share): RedirectResponse
    {
        $this->authorize('reject', $share);

        $collectionName = $share->collection->name;
        $share->delete();

        return redirect()->route('collections.index')
            ->with('success', "Collection invitation to '{$collectionName}' declined.");
    }

    public function destroy(Collection $collection, CollectionShare $share): RedirectResponse
    {
        $this->authorize('delete', $share);

        $userName = $share->user->name;
        $share->delete();

        return back()->with('success', "Access removed for {$userName}.");
    }
}
