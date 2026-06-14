<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['proposal_id', 'type', 'channel', 'metadata'])]
class ProposalEvent extends Model
{
    use HasFactory;

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }

    public function proposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class);
    }
}
