<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Card extends Model
{
    protected $fillable = [
        'scryfall_id',
        'oracle_id',
        'name',
        'mana_cost',
        'cmc',
        'type_line',
        'oracle_text',
        'power',
        'toughness',
        'colors',
        'color_identity',
        'keywords',
        'set_code',
        'set_name',
        'rarity',
        'image_uri_small',
        'image_uri_normal',
        'image_uri_large',
        'prices',
        'is_multi_faced',
        'card_faces',
        'legalities',
        'released_at',
        'last_synced_at',
    ];

    protected $casts = [
        'colors' => 'array',
        'color_identity' => 'array',
        'keywords' => 'array',
        'prices' => 'array',
        'card_faces' => 'array',
        'legalities' => 'array',
        'is_multi_faced' => 'boolean',
        'released_at' => 'date',
        'last_synced_at' => 'datetime',
        'cmc' => 'decimal:2',
    ];

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_card')
            ->withPivot('quantity', 'is_foil', 'notes', 'added_at')
            ->withTimestamps();
    }

    public function needsSync(): bool
    {
        if (!$this->last_synced_at) {
            return true;
        }

        return $this->last_synced_at->diffInDays(now()) > 7;
    }

    public function getImageUrl(string $size = 'normal'): ?string
    {
        return match ($size) {
            'small' => $this->image_uri_small,
            'large' => $this->image_uri_large,
            default => $this->image_uri_normal,
        };
    }

    public function getPriceFormatted(string $currency = 'usd'): ?string
    {
        if (!$this->prices || !isset($this->prices[$currency])) {
            return null;
        }

        $price = $this->prices[$currency];

        if ($price === null) {
            return null;
        }

        return '$' . number_format((float) $price, 2);
    }
}
