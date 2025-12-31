<?php

return [
    'base_url' => env('SCRYFALL_BASE_URL', 'https://api.scryfall.com'),

    'rate_limit_delay' => env('SCRYFALL_RATE_LIMIT_DELAY', 100),

    'cache_duration_days' => env('SCRYFALL_CACHE_DURATION_DAYS', 7),

    'bulk_data_type' => env('SCRYFALL_BULK_DATA_TYPE', 'default_cards'),
];
