<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionCard extends Model
{
    protected $table = 'collection_card';

    protected $fillable = [
        'collection_id',
        'card_id',
        'quantity',
        'is_foil',
        'notes',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'is_foil' => 'boolean',
        'added_at' => 'datetime',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function card(): BelongsTo
    {
        return $this->belongsTo(Card::class);
    }

    public function getTotalValue(string $currency = 'usd'): float
    {
        if (!$this->card || !$this->card->prices || !isset($this->card->prices[$currency])) {
            return 0;
        }

        $price = $this->card->prices[$currency];

        if ($price === null) {
            return 0;
        }

        return (float) $price * $this->quantity;
    }
}
