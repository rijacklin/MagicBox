<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ $card->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <div class="aspect-[2.5/3.5] max-w-md mx-auto rounded-lg overflow-hidden shadow-lg">
                                <x-card-image :card="$card"
                                             size="large"
                                             class="w-full h-full object-cover" />
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div>
                                <h3 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
                                    {{ $card->name }}
                                </h3>
                                @if($card->mana_cost)
                                    <div class="mt-2">
                                        <x-mana-symbols :cost="$card->mana_cost" />
                                    </div>
                                @endif
                            </div>

                            <div>
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Type</h4>
                                <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $card->type_line }}</p>
                            </div>

                            @if($card->oracle_text)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Text</h4>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100 whitespace-pre-line">{{ $card->oracle_text }}</p>
                                </div>
                            @endif

                            @if($card->power && $card->toughness)
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Power/Toughness</h4>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $card->power }} / {{ $card->toughness }}</p>
                                </div>
                            @endif

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Set</h4>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100">{{ $card->set_name }}</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ strtoupper($card->set_code) }}</p>
                                </div>

                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Rarity</h4>
                                    <p class="mt-1 text-gray-900 dark:text-gray-100 capitalize">{{ $card->rarity }}</p>
                                </div>
                            </div>

                            @if($card->prices && array_filter($card->prices))
                                <div>
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide">Prices</h4>
                                    <div class="mt-2 space-y-1">
                                        @if($card->prices['usd'])
                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                <span class="font-medium">USD:</span> ${{ $card->prices['usd'] }}
                                            </p>
                                        @endif
                                        @if($card->prices['usd_foil'] ?? null)
                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                <span class="font-medium">USD Foil:</span> ${{ $card->prices['usd_foil'] }}
                                            </p>
                                        @endif
                                        @if($card->prices['eur'] ?? null)
                                            <p class="text-sm text-gray-900 dark:text-gray-100">
                                                <span class="font-medium">EUR:</span> €{{ $card->prices['eur'] }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @php
                                $userCollections = Auth::user()->ownedCollections()->orderBy('name')->get();
                            @endphp

                            @if($userCollections->isNotEmpty())
                                <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                                    <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-3">
                                        Add to Collection
                                    </h4>
                                    <form method="POST" action="{{ route('collections.cards.store', ':collection_id') }}" id="addToCollectionForm">
                                        @csrf
                                        <input type="hidden" name="card_id" value="{{ $card->id }}">
                                        <div class="flex gap-2">
                                            <select name="collection_id"
                                                    id="collectionSelect"
                                                    required
                                                    class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm">
                                                <option value="">Select a collection...</option>
                                                @foreach($userCollections as $collection)
                                                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                                @endforeach
                                            </select>
                                            <input type="number"
                                                   name="quantity"
                                                   value="1"
                                                   min="1"
                                                   max="999"
                                                   required
                                                   class="w-20 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 shadow-sm"
                                                   placeholder="Qty">
                                            <button type="submit"
                                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                                Add
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                <script>
                                    document.getElementById('collectionSelect').addEventListener('change', function() {
                                        const form = document.getElementById('addToCollectionForm');
                                        const action = form.action.replace(':collection_id', this.value);
                                        form.action = action;
                                    });
                                </script>
                            @endif

                            <div class="pt-4 border-t border-gray-200 dark:border-gray-700 mt-6">
                                <a href="{{ route('cards.index') }}"
                                   class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Back to Search
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
