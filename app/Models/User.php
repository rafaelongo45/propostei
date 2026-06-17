<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'google_id', 'avatar', 'auth_provider', 'company_name', 'company_logo_path', 'payment_pix_key', 'payment_pix_key_type', 'payment_recipient_name', 'payment_link', 'payment_instructions', 'plan', 'subscription_status', 'premium_ends_at', 'is_admin'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    public const PIX_KEY_TYPES = [
        'cpf' => 'CPF',
        'cnpj' => 'CNPJ',
        'email' => 'E-mail',
        'phone' => 'Telefone',
        'random' => 'Chave aleatória',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'premium_ends_at' => 'datetime',
            'is_admin' => 'boolean',
        ];
    }

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class);
    }

    public function premiumInterests(): HasMany
    {
        return $this->hasMany(PremiumInterest::class);
    }

    public function userEvents(): HasMany
    {
        return $this->hasMany(UserEvent::class);
    }

    public function isPremium(): bool
    {
        return $this->plan === 'premium'
            && $this->subscription_status === 'active'
            && ($this->premium_ends_at === null || $this->premium_ends_at->isFuture());
    }

    public function isFree(): bool
    {
        return ! $this->isPremium();
    }

    public function proposalsLimit(): ?int
    {
        return $this->isPremium() ? null : 5;
    }

    public function activeProposalsCount(): int
    {
        return $this->proposals()->count();
    }

    public function canCreateProposal(): bool
    {
        $limit = $this->proposalsLimit();

        return $limit === null || $this->activeProposalsCount() < $limit;
    }

    public function planUsage(): array
    {
        $used = $this->activeProposalsCount();
        $limit = $this->proposalsLimit();

        return [
            'plan' => $this->isPremium() ? 'premium' : 'free',
            'label' => $this->isPremium() ? 'Premium' : 'Grátis',
            'subscription_status' => $this->subscription_status,
            'used' => $used,
            'limit' => $limit,
            'remaining' => $limit === null ? null : max(0, $limit - $used),
            'can_create_proposal' => $this->canCreateProposal(),
            'is_premium' => $this->isPremium(),
            'premium_price' => 'R$ 19,90/mês',
        ];
    }

    public function paymentDetails(): array
    {
        if (
            blank($this->payment_pix_key)
            && blank($this->payment_recipient_name)
            && blank($this->payment_link)
            && blank($this->payment_instructions)
        ) {
            return [];
        }

        return [
            'pix_key' => $this->payment_pix_key,
            'pix_key_type' => $this->payment_pix_key_type,
            'pix_key_type_label' => $this->payment_pix_key_type
                ? self::PIX_KEY_TYPES[$this->payment_pix_key_type] ?? $this->payment_pix_key_type
                : null,
            'recipient_name' => $this->payment_recipient_name,
            'link' => $this->payment_link,
            'instructions' => $this->payment_instructions,
        ];
    }

    protected function companyLogoUrl(): Attribute
    {
        return Attribute::get(fn () => $this->company_logo_path
            ? asset('storage/'.$this->company_logo_path)
            : null);
    }
}
