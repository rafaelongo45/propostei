<?php

namespace App\Models;

use Database\Factories\ProposalFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

#[Fillable(['user_id', 'client_id', 'title', 'introduction', 'valid_until', 'delivery_estimate', 'status', 'sent_at', 'sent_channel', 'accepted_at', 'accepted_by_name', 'accepted_by_email', 'accepted_by_ip', 'acceptance_notes', 'rejected_at', 'rejected_by_name', 'rejected_by_email', 'rejected_by_ip', 'rejection_reason', 'internal_notes', 'public_token', 'manual_discount'])]
class Proposal extends Model
{
    /** @use HasFactory<ProposalFactory> */
    use HasFactory, SoftDeletes;

    public const STATUSES = ['draft', 'sent', 'accepted', 'rejected'];

    protected function casts(): array
    {
        return [
            'valid_until' => 'date',
            'sent_at' => 'datetime',
            'accepted_at' => 'datetime',
            'rejected_at' => 'datetime',
            'manual_discount' => 'decimal:2',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Proposal $proposal): void {
            $proposal->public_token ??= Str::random(32);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class)->withTrashed();
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProposalItem::class)->orderBy('sort_order');
    }

    public function events(): HasMany
    {
        return $this->hasMany(ProposalEvent::class)->latest();
    }

    public function markAsSent(string $channel): void
    {
        $updates = [
            'sent_at' => now(),
            'sent_channel' => $channel,
        ];

        if ($this->status === 'draft') {
            $updates['status'] = 'sent';
        }

        if (! in_array($this->status, ['accepted', 'rejected'], true)) {
            $this->update($updates);
        }
    }

    public function recordEvent(string $type, ?string $channel = null, ?array $metadata = null): ProposalEvent
    {
        return $this->events()->create([
            'type' => $type,
            'channel' => $channel,
            'metadata' => $metadata,
        ]);
    }

    public function getSubtotalAttribute(): float
    {
        return (float) $this->items->sum(fn (ProposalItem $item) => $item->subtotal);
    }

    public function getTotalAttribute(): float
    {
        return max(0, $this->subtotal - (float) $this->manual_discount);
    }
}
