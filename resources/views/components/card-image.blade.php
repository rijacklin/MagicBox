@if($card && $card->getImageUrl($size))
    <img src="{{ $card->getImageUrl($size) }}"
         alt="{{ $card->name }}"
         class="{{ $class }}"
         loading="lazy">
@else
    <div class="bg-gray-200 dark:bg-gray-700 rounded-lg flex items-center justify-center {{ $class }}">
        <span class="text-gray-500 dark:text-gray-400 text-sm">No Image</span>
    </div>
@endif