<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollectionShare extends Model
{
    protected $fillable = [
        'collection_id',
        'user_id',
        'shared_by_user_id',
        'permission_level',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function collection(): BelongsTo
    {
        return $this->belongsTo(Collection::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function sharedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_by_user_id');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->whereNull('accepted_at');
    }

    public function scopeAccepted(Builder $query): Builder
    {
        return $query->whereNotNull('accepted_at');
    }

    public function accept(): void
    {
        $this->accepted_at = now();
        $this->save();
    }

    public function isAccepted(): bool
    {
        return $this->accepted_at !== null;
    }

    public function canEdit(): bool
    {
        return $this->permission_level === 'edit';
    }
}
