<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'name', 'email', 'current_proposals_count', 'source', 'status'])]
class PremiumInterest extends Model
{
    public const STATUSES = ['interested', 'contacted', 'converted', 'dismissed'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
