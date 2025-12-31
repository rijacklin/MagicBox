<div class="inline-flex items-center gap-0.5">
    @foreach($symbols() as $symbol)
        <span class="inline-flex items-center justify-center w-5 h-5 rounded-full text-xs font-bold
            @if(is_numeric($symbol))
                bg-gray-300 text-gray-700
            @elseif($symbol === 'W')
                bg-yellow-100 text-yellow-800 border border-yellow-300
            @elseif($symbol === 'U')
                bg-blue-100 text-blue-800 border border-blue-300
            @elseif($symbol === 'B')
                bg-gray-800 text-white border border-gray-600
            @elseif($symbol === 'R')
                bg-red-100 text-red-800 border border-red-300
            @elseif($symbol === 'G')
                bg-green-100 text-green-800 border border-green-300
            @else
                bg-gray-200 text-gray-600
            @endif
        ">
            {{ $symbol }}
        </span>
    @endforeach
</div>