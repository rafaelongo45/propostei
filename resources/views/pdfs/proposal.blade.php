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
        }

        .brand {
            color: #111827;
            font-size: 24px;
            font-weight: 700;
            letter-spacing: 0;
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
        }

        .box {
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 14px;
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

        .status {
            border-radius: 999px;
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            padding: 5px 10px;
        }

        .status-draft {
            background: #f1f5f9;
            color: #334155;
        }

        .status-sent {
            background: #eff6ff;
            color: #1d4ed8;
        }

        .status-accepted {
            background: #ecfdf5;
            color: #047857;
        }

        .status-rejected {
            background: #fff1f2;
            color: #be123c;
        }

        table {
            border-collapse: collapse;
            width: 100%;
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
            margin-left: auto;
            margin-top: 14px;
            width: 260px;
        }

        .total-row {
            display: table;
            width: 100%;
        }

        .total-row div {
            display: table-cell;
            padding: 5px 0;
        }

        .total-final {
            border-top: 1px solid #d1d5db;
            font-size: 15px;
            font-weight: 700;
            margin-top: 6px;
            padding-top: 8px;
        }

        .note {
            white-space: pre-line;
        }

        .footer {
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
            margin-top: 28px;
            padding-top: 12px;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $date = fn ($value) => $value ? $value->format('d/m/Y') : 'Não informada';
    $status = $proposal->status;
@endphp

<div class="header">
    <div class="brand">propostei</div>
    <div class="proposal-meta">Proposta comercial</div>
    <h1>{{ $proposal->title }}</h1>
    <span class="status status-{{ $status }}">{{ $statusLabels[$status] ?? $status }}</span>
</div>

<div class="grid">
    <div class="grid-cell">
        <div class="box">
            <h2>Cliente</h2>
            <div class="label">Nome</div>
            <div class="value">{{ $proposal->client->name }}</div>

            @if ($proposal->client->company)
                <div style="margin-top: 8px;">
                    <div class="label">Empresa</div>
                    <div>{{ $proposal->client->company }}</div>
                </div>
            @endif

            @if ($proposal->client->email)
                <div style="margin-top: 8px;">
                    <div class="label">E-mail</div>
                    <div>{{ $proposal->client->email }}</div>
                </div>
            @endif

            @if ($proposal->client->phone)
                <div style="margin-top: 8px;">
                    <div class="label">Telefone</div>
                    <div>{{ $proposal->client->phone }}</div>
                </div>
            @endif
        </div>
    </div>
    <div class="grid-cell">
        <div class="box">
            <h2>Dados da proposta</h2>
            <div class="grid">
                <div class="grid-cell">
                    <div class="label">Validade</div>
                    <div class="value">{{ $date($proposal->valid_until) }}</div>
                </div>
                <div class="grid-cell">
                    <div class="label">Prazo</div>
                    <div class="value">{{ $proposal->delivery_estimate ?: 'Não informado' }}</div>
                </div>
            </div>
            <div style="margin-top: 10px;">
                <div class="label">Gerada em</div>
                <div>{{ now()->format('d/m/Y H:i') }}</div>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <h2>Introdução</h2>
    <div class="box note">
        {{ $proposal->introduction ?: 'Sem descrição informada.' }}
    </div>
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

    <div class="totals">
        <div class="total-row">
            <div>Subtotal</div>
            <div class="right">{{ $money($proposal->subtotal) }}</div>
        </div>
        @if ((float) $proposal->manual_discount > 0)
            <div class="total-row">
                <div>Desconto</div>
                <div class="right">{{ $money($proposal->manual_discount) }}</div>
            </div>
        @endif
        <div class="total-row total-final">
            <div>Total</div>
            <div class="right">{{ $money($proposal->total) }}</div>
        </div>
    </div>
</div>

@if ($proposal->status === 'accepted')
    <div class="section">
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
    <div class="section">
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
