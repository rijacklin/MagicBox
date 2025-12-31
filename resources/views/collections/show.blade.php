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
