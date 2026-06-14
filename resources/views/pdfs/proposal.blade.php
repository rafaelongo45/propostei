<!doctype html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $proposal->title }}</title>
    <style>
        @page {
            margin: 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            line-height: 1.45;
            margin: 0;
        }

        .header {
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 24px;
            padding-bottom: 18px;
            page-break-inside: avoid;
        }

        .brand {
            color: #111827;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0;
        }

        .brand-logo {
            display: block;
            margin-bottom: 10px;
            max-height: 64px;
            max-width: 220px;
        }

        .creator {
            color: #030712;
            font-size: 16px;
            margin-top: 4px;
        }

        .proposal-meta {
            color: #4b5563;
            font-size: 11px;
            margin-top: 10px;
            text-transform: uppercase;
        }

        h1 {
            color: #030712;
            font-size: 24px;
            line-height: 1.2;
            margin: 12px 0 6px;
        }

        h2 {
            color: #111827;
            font-size: 14px;
            margin: 0 0 10px;
        }

        p {
            margin: 0;
        }

        .muted {
            color: #6b7280;
        }

        .section {
            margin-top: 22px;
            page-break-inside: avoid;
        }

        .box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
            page-break-inside: avoid;
        }

        .grid {
            display: table;
            table-layout: fixed;
            width: 100%;
        }

        .grid-cell {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .grid-cell + .grid-cell {
            padding-left: 14px;
        }

        .info-section + .info-section {
            margin-top: 12px;
        }

        .info-list {
            border-top: 1px solid #f3f4f6;
            padding-top: 2px;
        }

        .info-row {
            border-bottom: 1px solid #f3f4f6;
            padding: 7px 0;
        }

        .info-row:last-child {
            border-bottom: 0;
            padding-bottom: 0;
        }

        .info-label {
            color: #6b7280;
            display: inline-block;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            width: 96px;
        }

        .info-value {
            color: #111827;
            font-weight: 700;
        }

        .label {
            color: #6b7280;
            font-size: 10px;
            margin-bottom: 2px;
            text-transform: uppercase;
        }

        .value {
            color: #111827;
            font-weight: 700;
        }

        table {
            border-collapse: collapse;
            page-break-inside: auto;
            width: 100%;
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
        }

        tr {
            page-break-after: auto;
            page-break-inside: avoid;
        }

        th {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
            padding: 9px 8px;
            text-align: left;
            text-transform: uppercase;
        }

        td {
            border-bottom: 1px solid #f3f4f6;
            padding: 10px 8px;
            vertical-align: top;
        }

        .right {
            text-align: right;
        }

        .totals {
            border: 1px solid #e5e7eb;
            margin-top: 14px;
            page-break-inside: avoid;
            padding: 10px 12px;
            width: 100%;
        }

        .total-line {
            display: table;
            table-layout: fixed;
            width: 100%;
        }

        .total-item {
            display: table-cell;
            padding-right: 14px;
            vertical-align: middle;
            white-space: nowrap;
        }

        .total-item:last-child {
            padding-right: 0;
        }

        .total-label {
            color: #6b7280;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
        }

        .total-value {
            color: #111827;
            font-weight: 700;
        }

        .total-final {
            font-size: 15px;
            font-weight: 700;
        }

        .note {
            white-space: pre-line;
        }

        .intro-text {
            margin: 0;
            padding: 12px 14px;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
            margin-top: 28px;
            padding-top: 12px;
            text-align: center;
        }

        .client-info,
        .proposal-info,
        .summary,
        .payment,
        .acceptance,
        .rejection {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $date = fn ($value) => $value ? $value->format('d/m/Y') : 'Não informada';
    $creatorName = $proposal->user?->company_name ?: $proposal->user?->name ?: 'Prestador';
    $payment = $proposal->user?->paymentDetails() ?: [];
@endphp

<div class="header">
    @if ($companyLogoPath)
        <img src="{{ $companyLogoPath }}" alt="{{ $creatorName }}" class="brand-logo">
        <div class="brand">{{ $creatorName }}</div>
    @else
        <div class="brand">{{ $creatorName }}</div>
    @endif
    <div class="proposal-meta">Proposta comercial</div>
    <h1>{{ $proposal->title }}</h1>
</div>

<div class="box client-info info-section">
    <h2>Cliente</h2>
    <div class="info-list">
        <div class="info-row">
            <span class="info-label">Nome</span>
            <span class="info-value">{{ $proposal->client->name }}</span>
        </div>

        @if ($proposal->client->company)
            <div class="info-row">
                <span class="info-label">Empresa</span>
                <span>{{ $proposal->client->company }}</span>
            </div>
        @endif

        @if ($proposal->client->email)
            <div class="info-row">
                <span class="info-label">E-mail</span>
                <span>{{ $proposal->client->email }}</span>
            </div>
        @endif

        @if ($proposal->client->phone)
            <div class="info-row">
                <span class="info-label">Telefone</span>
                <span>{{ $proposal->client->phone }}</span>
            </div>
        @endif
    </div>
</div>

<div class="box proposal-info info-section">
    <h2>Dados da proposta</h2>
    <div class="info-list">
        <div class="info-row">
            <span class="info-label">Validade</span>
            <span class="info-value">{{ $date($proposal->valid_until) }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Prazo</span>
            <span class="info-value">{{ $proposal->delivery_estimate ?: 'Não informado' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Gerada em</span>
            <span>{{ now()->format('d/m/Y H:i') }}</span>
        </div>
    </div>
</div>

<div class="section">
    <h2>Introdução</h2>
    <div class="box note intro-text">{{ trim($proposal->introduction ?: 'Sem descrição informada.') }}</div>
</div>

<div class="section">
    <h2>Itens e valores</h2>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th class="right" style="width: 70px;">Qtd.</th>
                <th class="right" style="width: 110px;">Unitário</th>
                <th class="right" style="width: 110px;">Subtotal</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($proposal->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ number_format((float) $item->quantity, 2, ',', '.') }}</td>
                    <td class="right">{{ $money($item->unit_price) }}</td>
                    <td class="right"><strong>{{ $money($item->subtotal) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals summary">
        <div class="total-line">
            <div class="total-item">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">{{ $money($proposal->subtotal) }}</span>
            </div>
            <div class="total-item">
                <span class="total-label">Desconto:</span>
                <span class="total-value">{{ $money($proposal->manual_discount) }}</span>
            </div>
            <div class="total-item">
                <span class="total-label">Total:</span>
                <span class="total-value total-final">{{ $money($proposal->total) }}</span>
            </div>
        </div>
    </div>
</div>

@if ($payment)
    <div class="section payment">
        <div class="box">
            <h2>Pagamento</h2>
            <div class="info-list">
                @if ($payment['recipient_name'] ?? null)
                    <div class="info-row">
                        <span class="info-label">Favorecido</span>
                        <span class="info-value">{{ $payment['recipient_name'] }}</span>
                    </div>
                @endif

                @if ($payment['pix_key'] ?? null)
                    <div class="info-row">
                        <span class="info-label">Chave Pix</span>
                        <span class="info-value">{{ $payment['pix_key'] }}</span>
                        @if ($payment['pix_key_type_label'] ?? null)
                            <span class="muted">({{ $payment['pix_key_type_label'] }})</span>
                        @endif
                    </div>
                @endif

                @if ($payment['link'] ?? null)
                    <div class="info-row">
                        <span class="info-label">Link</span>
                        <span>{{ $payment['link'] }}</span>
                    </div>
                @endif
            </div>

            @if ($payment['instructions'] ?? null)
                <div style="margin-top: 10px;">
                    <div class="label">Instruções de pagamento</div>
                    <div class="note">{{ $payment['instructions'] }}</div>
                </div>
            @endif
        </div>
    </div>
@endif

@if ($proposal->status === 'accepted')
    <div class="section acceptance">
        <div class="box">
            <h2>Aceite da proposta</h2>
            <div class="grid">
                <div class="grid-cell">
                    <div class="label">Aceita em</div>
                    <div class="value">{{ $proposal->accepted_at?->format('d/m/Y H:i') ?: 'Não registrado' }}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Responsável</div>
                    <div class="value">{{ $proposal->accepted_by_name ?: 'Não registrado' }}</div>
                </div>
            </div>
            @if ($proposal->accepted_by_email)
                <div style="margin-top: 8px;">
                    <div class="label">E-mail</div>
                    <div>{{ $proposal->accepted_by_email }}</div>
                </div>
            @endif
            @if ($proposal->acceptance_notes)
                <div style="margin-top: 10px;">
                    <div class="label">Observações do cliente</div>
                    <div class="note">{{ $proposal->acceptance_notes }}</div>
                </div>
            @endif
        </div>
    </div>
@endif

@if ($proposal->status === 'rejected')
    <div class="section rejection">
        <div class="box">
            <h2>Recusa da proposta</h2>
            <div class="grid">
                <div class="grid-cell">
                    <div class="label">Recusada em</div>
                    <div class="value">{{ $proposal->rejected_at?->format('d/m/Y H:i') ?: 'Não registrado' }}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Responsável</div>
                    <div class="value">{{ $proposal->rejected_by_name ?: 'Não registrado' }}</div>
                </div>
            </div>
            @if ($proposal->rejected_by_email)
                <div style="margin-top: 8px;">
                    <div class="label">E-mail</div>
                    <div>{{ $proposal->rejected_by_email }}</div>
                </div>
            @endif
            @if ($proposal->rejection_reason)
                <div style="margin-top: 10px;">
                    <div class="label">Motivo da recusa</div>
                    <div class="note">{{ $proposal->rejection_reason }}</div>
                </div>
            @endif
        </div>
    </div>
@endif

<div class="footer">
    PDF gerado pelo propostei. Observações internas não são incluídas neste documento.
</div>
</body>
</html>
