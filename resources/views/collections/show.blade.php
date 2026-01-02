<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ $collection->name }}
            </h2>
            <div class="flex gap-2">
                @can('update', $collection)
                    <a href="{{ route('cards.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                        Add Cards
                    </a>
                @endcan
                @can('delete', $collection)
                    <form method="POST" action="{{ route('collections.destroy', $collection) }}" onsubmit="return confirm('Are you sure you want to delete this collection?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 transition ease-in-out duration-150">
                            Delete
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($collection->description)
                        <p class="text-gray-600 dark:text-gray-400 mb-4">
                            {{ $collection->description }}
                        </p>
                    @endif

                    <div class="flex gap-6 text-sm text-gray-600 dark:text-gray-400">
                        <div>
                            <span class="font-semibold">Total Cards:</span>
                            {{ $collection->getTotalCards() }}
                        </div>
                        <div>
                            <span class="font-semibold">Owner:</span>
                            {{ $collection->owner->name }}
                        </div>
                        @if($collection->is_public)
                            <div>
                                <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded text-xs">
                                    Public
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            @if($isOwner)
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Share Collection
                        </h3>

                        <form method="POST" action="{{ route('collections.shares.store', $collection) }}" class="mb-6">
                            @csrf
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div class="md:col-span-2">
                                    <x-input-label for="email" value="User Email" />
                                    <x-text-input
                                        id="email"
                                        name="email"
                                        type="email"
                                        class="mt-1 block w-full"
                                        placeholder="Enter user's email address"
                                        :value="old('email')"
                                        required
                                    />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="permission_level" value="Permission" />
                                    <select
                                        id="permission_level"
                                        name="permission_level"
                                        class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                                        required
                                    >
                                        <option value="view" {{ old('permission_level') === 'view' ? 'selected' : '' }}>View Only</option>
                                        <option value="edit" {{ old('permission_level') === 'edit' ? 'selected' : '' }}>Can Edit</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('permission_level')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                                    Share Collection
                                </button>
                            </div>
                        </form>

                        @if($collection->shares->isNotEmpty())
                            <div class="border-t dark:border-gray-700 pt-4">
                                <h4 class="font-semibold text-gray-900 dark:text-gray-100 mb-3">
                                    Shared With ({{ $collection->shares->count() }})
                                </h4>

                                <div class="space-y-2">
                                    @foreach($collection->shares as $share)
                                        <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <span class="font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $share->user->name }}
                                                    </span>
                                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $share->user->email }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-2 mt-1">
                                                    <span class="text-xs px-2 py-1 rounded {{ $share->permission_level === 'edit' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-600 dark:text-gray-200' }}">
                                                        {{ ucfirst($share->permission_level) }}
                                                    </span>
                                                    @if($share->isAccepted())
                                                        <span class="text-xs px-2 py-1 rounded bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                            Accepted
                                                        </span>
                                                    @else
                                                        <span class="text-xs px-2 py-1 rounded bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                            Pending
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <form method="POST" action="{{ route('collections.shares.destroy', [$collection, $share]) }}" onsubmit="return confirm('Remove this user from the collection?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 text-sm font-medium">
                                                    Remove
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400 border-t dark:border-gray-700 pt-4">
                                This collection is not currently shared with anyone.
                            </p>
                        @endif
                    </div>
                </div>
            @else
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div>
                                <h4 class="font-semibold text-blue-900 dark:text-blue-100">
                                    Shared Collection
                                </h4>
                                <p class="text-sm text-blue-800 dark:text-blue-200 mt-1">
                                    This collection is owned by {{ $collection->owner->name }}.
                                    You have {{ $collection->canBeEditedBy(Auth::user()) ? 'edit' : 'view-only' }} access.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        Cards in Collection
                    </h3>

                    @if($collection->cards->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Card
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Set
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Quantity
                                        </th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                            Foil
                                        </th>
                                        @can('update', $collection)
                                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                                Actions
                                            </th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($collection->cards as $card)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-12 w-8">
                                                        <x-card-image :card="$card" size="small" class="h-12 w-8 rounded" />
                                                    </div>
                                                    <div class="ml-4">
                                                        <a href="{{ route('cards.show', $card) }}"
                                                           class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                            {{ $card->name }}
                                                        </a>
                                                        @if($card->mana_cost)
                                                            <div class="mt-1">
                                                                <x-mana-symbols :cost="$card->mana_cost" />
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $card->set_name }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                                {{ $card->pivot->quantity }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @if($card->pivot->is_foil)
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                        Foil
                                                    </span>
                                                @endif
                                            </td>
                                            @can('update', $collection)
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <form method="POST" action="{{ route('collections.cards.destroy', [$collection, $card]) }}"
                                                          onsubmit="return confirm('Remove this card from the collection?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                            Remove
                                                        </button>
                                                    </form>
                                                </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                            This collection is empty.
                            @can('update', $collection)
                                <a href="{{ route('cards.index') }}" class="text-indigo-600 hover:text-indigo-700">
                                    Search for cards to add
                                </a>
                            @endcan
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
