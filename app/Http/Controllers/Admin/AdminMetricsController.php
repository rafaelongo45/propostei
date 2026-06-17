<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserEvent;
use Illuminate\Database\Eloquent\Builder;
use Inertia\Inertia;
use Inertia\Response;

class AdminMetricsController extends Controller
{
    private const EVENTS = [
        'landing_viewed' => 'Visitas na landing',
        'register_clicked' => 'Cliques em cadastro',
        'user_registered' => 'Usuários cadastrados',
        'client_created' => 'Clientes criados',
        'proposal_created' => 'Propostas criadas',
        'proposal_sent_whatsapp' => 'Envios por WhatsApp',
        'proposal_pdf_downloaded' => 'PDFs baixados',
        'proposal_public_viewed' => 'Visualizações públicas',
        'proposal_accepted' => 'Propostas aceitas',
        'proposal_rejected' => 'Propostas recusadas',
        'premium_interest_clicked' => 'Interesses Premium',
    ];

    public function index(): Response
    {
        $periods = [
            'today' => ['label' => 'Hoje', 'start' => now()->startOfDay()],
            'seven_days' => ['label' => 'Últimos 7 dias', 'start' => now()->subDays(6)->startOfDay()],
            'thirty_days' => ['label' => 'Últimos 30 dias', 'start' => now()->subDays(29)->startOfDay()],
            'total' => ['label' => 'Total', 'start' => null],
        ];

        $metrics = collect(self::EVENTS)
            ->map(fn (string $label, string $event) => [
                'event' => $event,
                'label' => $label,
                'counts' => collect($periods)
                    ->map(fn (array $period) => $this->eventCount($event, $period['start']))
                    ->all(),
            ])
            ->values()
            ->all();

        $funnelCounts = collect(self::EVENTS)
            ->mapWithKeys(fn (string $label, string $event) => [$event => $this->eventCount($event, now()->subDays(29)->startOfDay())])
            ->all();

        return Inertia::render('Admin/Metrics/Index', [
            'periods' => collect($periods)->map(fn ($period) => $period['label'])->all(),
            'metrics' => $metrics,
            'funnel' => $this->funnel($funnelCounts),
            'recentEvents' => $this->recentEvents(),
            'recentUsers' => $this->recentUsers(),
        ]);
    }

    private function eventCount(string $event, $start): int
    {
        return UserEvent::query()
            ->where('event', $event)
            ->when($start, fn (Builder $query) => $query->where('created_at', '>=', $start))
            ->count();
    }

    private function funnel(array $counts): array
    {
        return [
            ['label' => 'Visitantes/visitas landing', 'count' => $counts['landing_viewed'], 'rate' => null],
            ['label' => 'Cliques em cadastro', 'count' => $counts['register_clicked'], 'rate' => $this->rate($counts['register_clicked'], $counts['landing_viewed'])],
            ['label' => 'Cadastros concluídos', 'count' => $counts['user_registered'], 'rate' => $this->rate($counts['user_registered'], $counts['register_clicked'])],
            ['label' => 'Clientes criados', 'count' => $counts['client_created'], 'rate' => $this->rate($counts['client_created'], $counts['user_registered'])],
            ['label' => 'Propostas criadas', 'count' => $counts['proposal_created'], 'rate' => $this->rate($counts['proposal_created'], $counts['user_registered'])],
            ['label' => 'Propostas enviadas por WhatsApp', 'count' => $counts['proposal_sent_whatsapp'], 'rate' => $this->rate($counts['proposal_sent_whatsapp'], $counts['proposal_created'])],
            ['label' => 'Propostas visualizadas pelo cliente', 'count' => $counts['proposal_public_viewed'], 'rate' => $this->rate($counts['proposal_public_viewed'], $counts['proposal_sent_whatsapp'])],
            ['label' => 'Propostas aceitas', 'count' => $counts['proposal_accepted'], 'rate' => $this->rate($counts['proposal_accepted'], $counts['proposal_public_viewed'])],
            ['label' => 'Interesse Premium', 'count' => $counts['premium_interest_clicked'], 'rate' => $this->rate($counts['premium_interest_clicked'], $counts['user_registered'])],
        ];
    }

    private function rate(int $value, int $base): ?float
    {
        return $base > 0 ? round(($value / $base) * 100, 1) : null;
    }

    private function recentEvents(): array
    {
        return UserEvent::query()
            ->with('user:id,name,email')
            ->latest()
            ->limit(15)
            ->get()
            ->map(fn (UserEvent $event) => [
                'id' => $event->id,
                'event' => $event->event,
                'label' => self::EVENTS[$event->event] ?? $event->event,
                'user' => $event->user ? [
                    'name' => $event->user->name,
                    'email' => $event->user->email,
                ] : null,
                'metadata' => $event->metadata,
                'created_at' => $event->created_at?->format('d/m/Y H:i'),
            ])
            ->all();
    }

    private function recentUsers(): array
    {
        return User::query()
            ->withCount([
                'clients',
                'proposals',
                'proposals as accepted_proposals_count' => fn (Builder $query) => $query->where('status', 'accepted'),
                'userEvents',
                'premiumInterests',
            ])
            ->latest()
            ->limit(12)
            ->get(['id', 'name', 'email', 'created_at'])
            ->map(fn (User $user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'created_at' => $user->created_at?->format('d/m/Y H:i'),
                'clients_count' => $user->clients_count,
                'proposals_count' => $user->proposals_count,
                'accepted_proposals_count' => $user->accepted_proposals_count,
                'events_count' => $user->user_events_count,
                'has_premium_interest' => $user->premium_interests_count > 0,
            ])
            ->all();
    }
}
