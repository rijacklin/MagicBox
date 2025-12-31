<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collection extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cards(): BelongsToMany
    {
        return $this->belongsToMany(Card::class, 'collection_card')
            ->withPivot('quantity', 'is_foil', 'notes', 'added_at')
            ->withTimestamps();
    }

    public function shares(): HasMany
    {
        return $this->hasMany(CollectionShare::class);
    }

    public function sharedWithUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'collection_shares')
            ->withPivot('permission_level', 'accepted_at', 'shared_by_user_id')
            ->withTimestamps();
    }

    public function scopeOwnedBy(Builder $query, User $user): Builder
    {
        return $query->where('user_id', $user->id);
    }

    public function scopeSharedWith(Builder $query, User $user): Builder
    {
        return $query->whereHas('shares', function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->whereNotNull('accepted_at');
        });
    }

    public function scopeAccessibleBy(Builder $query, User $user): Builder
    {
        return $query->where(function ($q) use ($user) {
            $q->where('user_id', $user->id)
              ->orWhereHas('shares', function ($shareQuery) use ($user) {
                  $shareQuery->where('user_id', $user->id)
                             ->whereNotNull('accepted_at');
              });
        });
    }

    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    public function isSharedWith(User $user): bool
    {
        return $this->shares()
            ->where('user_id', $user->id)
            ->whereNotNull('accepted_at')
            ->exists();
    }

    public function canBeAccessedBy(User $user): bool
    {
        return $this->isOwnedBy($user) || $this->isSharedWith($user);
    }

    public function canBeEditedBy(User $user): bool
    {
        if ($this->isOwnedBy($user)) {
            return true;
        }

        return $this->shares()
            ->where('user_id', $user->id)
            ->where('permission_level', 'edit')
            ->whereNotNull('accepted_at')
            ->exists();
    }

    public function getTotalCards(): int
    {
        return $this->cards()->sum('collection_card.quantity');
    }

    public function getTotalValue(string $currency = 'usd'): float
    {
        $total = 0;

        $this->cards->each(function ($card) use (&$total, $currency) {
            if ($card->prices && isset($card->prices[$currency]) && $card->prices[$currency] !== null) {
                $total += (float) $card->prices[$currency] * $card->pivot->quantity;
            }
        });

        return $total;
    }
}
