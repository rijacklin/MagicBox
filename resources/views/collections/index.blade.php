<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Collections') }}
            </h2>
            <a href="{{ route('collections.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Create Collection
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif

            @if($pendingShares->isNotEmpty())
                <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-4">
                            Pending Collection Invitations
                        </h3>

                        <div class="space-y-3">
                            @foreach($pendingShares as $share)
                                <div class="flex items-center justify-between p-4 bg-white dark:bg-gray-800 rounded-lg border border-yellow-200 dark:border-yellow-700">
                                    <div>
                                        <h4 class="font-semibold text-gray-900 dark:text-gray-100">
                                            {{ $share->collection->name }}
                                        </h4>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">
                                            Shared by {{ $share->sharedBy->name }} &bull;
                                            {{ ucfirst($share->permission_level) }} access
                                        </p>
                                    </div>
                                    <div class="flex gap-2">
                                        <form method="POST" action="{{ route('shares.accept', $share) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 transition ease-in-out duration-150">
                                                Accept
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('shares.reject', $share) }}">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-700 dark:text-gray-300 uppercase tracking-widest hover:bg-gray-50 dark:hover:bg-gray-700 transition ease-in-out duration-150">
                                                Decline
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                        My Collections
                    </h3>

                    @if($ownedCollections->isNotEmpty())
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($ownedCollections as $collection)
                                <a href="{{ route('collections.show', $collection) }}"
                                   class="block p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        {{ $collection->name }}
                                    </h4>
                                    @if($collection->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                            {{ $collection->description }}
                                        </p>
                                    @endif
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-gray-500 dark:text-gray-400">
                                            {{ $collection->cards_count }} {{ Str::plural('card', $collection->cards_count) }}
                                        </span>
                                        @if($collection->is_public)
                                            <span class="px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded text-xs">
                                                Public
                                            </span>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400 text-center py-8">
                            You haven't created any collections yet.
                            <a href="{{ route('collections.create') }}" class="text-indigo-600 hover:text-indigo-700">
                                Create your first collection
                            </a>
                        </p>
                    @endif
                </div>
            </div>

            @if($sharedCollections->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">
                            Shared With Me
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            @foreach($sharedCollections as $collection)
                                <a href="{{ route('collections.show', $collection) }}"
                                   class="block p-6 bg-gray-50 dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 hover:border-indigo-500 dark:hover:border-indigo-400 transition-colors">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                                        {{ $collection->name }}
                                    </h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                        Owned by {{ $collection->owner->name }}
                                    </p>
                                    @if($collection->description)
                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">
                                            {{ $collection->description }}
                                        </p>
                                    @endif
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $collection->cards_count }} {{ Str::plural('card', $collection->cards_count) }}
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
