<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Search Cards') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <form method="GET" action="{{ route('cards.index') }}" class="mb-6">
                        <div class="flex gap-2">
                            <input type="text"
                                   name="q"
                                   value="{{ $query ?? '' }}"
                                   placeholder="Search for cards..."
                                   class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                            <button type="submit"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                Search
                            </button>
                        </div>
                    </form>

                    @if($query)
                        <div class="mb-4">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                Found {{ $totalCards }} {{ Str::plural('card', $totalCards) }}
                                @if($query)
                                    for "{{ $query }}"
                                @endif
                            </p>
                        </div>
                    @endif

                    @if($cards->isNotEmpty())
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4">
                            @foreach($cards as $card)
                                <a href="{{ route('cards.show', $card) }}" class="group">
                                    <div class="aspect-[2.5/3.5] rounded-lg overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-200">
                                        <x-card-image :card="$card"
                                                     size="normal"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-200" />
                                    </div>
                                    <p class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100 truncate">
                                        {{ $card->name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                        {{ $card->set_name }}
                                    </p>
                                </a>
                            @endforeach
                        </div>

                        @if($hasMore)
                            <div class="mt-6 text-center">
                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                    More results available. Refine your search for better results.
                                </p>
                            </div>
                        @endif
                    @elseif($query)
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">No cards found for "{{ $query }}"</p>
                        </div>
                    @else
                        <div class="text-center py-12">
                            <p class="text-gray-500 dark:text-gray-400">
                                Enter a search query to find Magic: The Gathering cards
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
