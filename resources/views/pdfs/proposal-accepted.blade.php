<!doctype html>
<html lang="pt-BR">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ data_get($snapshot, 'proposal.title', 'Proposta aceita') }}</title>
    <style>
        @page { margin: 22px 26px; }
        * { box-sizing: border-box; }
        body {
            color: #111827;
            font-family: DejaVu Sans, sans-serif;
            font-size: 10.5px;
            line-height: 1.32;
            margin: 0;
        }
        .header {
            border-bottom: 1px solid #e5e7eb;
            margin-bottom: 12px;
            padding-bottom: 10px;
            page-break-inside: avoid;
        }
        .brand { color: #111827; font-size: 18px; font-weight: 700; }
        .brand-logo { display: block; margin-bottom: 6px; max-height: 46px; max-width: 180px; }
        .meta { color: #4b5563; font-size: 9px; margin-top: 5px; text-transform: uppercase; }
        h1 { color: #030712; font-size: 18px; line-height: 1.15; margin: 6px 0 0; }
        h2 { color: #111827; font-size: 12px; margin: 0 0 6px; }
        table { border-collapse: collapse; page-break-inside: auto; width: 100%; }
        thead { display: table-header-group; }
        tr { page-break-after: auto; page-break-inside: avoid; }
        th {
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
            padding: 6px 7px;
            text-align: left;
            text-transform: uppercase;
        }
        td { border-bottom: 1px solid #f3f4f6; padding: 6px 7px; vertical-align: top; }
        .right { text-align: right; }
        .section { margin-top: 10px; page-break-inside: avoid; }
        .box { border: 1px solid #e5e7eb; border-radius: 6px; padding: 9px 10px; page-break-inside: avoid; }
        .compact-grid { display: table; table-layout: fixed; width: 100%; }
        .compact-cell { display: table-cell; vertical-align: top; width: 50%; }
        .compact-cell + .compact-cell { padding-left: 10px; }
        .info-row { border-bottom: 1px solid #f3f4f6; padding: 4px 0; }
        .info-row:last-child { border-bottom: 0; padding-bottom: 0; }
        .info-label {
            color: #6b7280;
            display: inline-block;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            width: 112px;
        }
        .info-value { color: #111827; font-weight: 700; }
        .label { color: #6b7280; font-size: 9px; font-weight: 700; margin-bottom: 2px; text-transform: uppercase; }
        .value { color: #111827; font-weight: 700; }
        .note { white-space: pre-line; }
        .totals { border: 1px solid #e5e7eb; margin-top: 8px; padding: 7px 9px; page-break-inside: avoid; }
        .total-line { display: table; table-layout: fixed; width: 100%; }
        .total-item { display: table-cell; padding-right: 14px; white-space: nowrap; }
        .total-item:last-child { padding-right: 0; }
        .total-label { color: #6b7280; font-size: 9px; font-weight: 700; text-transform: uppercase; }
        .total-value { color: #111827; font-weight: 700; }
        .total-final { font-size: 13px; font-weight: 700; }
        .footer {
            border-top: 1px solid #e5e7eb;
            color: #6b7280;
            font-size: 10px;
            margin-top: 14px;
            padding-top: 8px;
            text-align: center;
        }
    </style>
</head>
<body>
@php
    $money = fn ($value) => 'R$ '.number_format((float) $value, 2, ',', '.');
    $date = function ($value, string $format = 'd/m/Y') {
        if (! $value) {
            return 'Não informada';
        }

        try {
            return \Carbon\Carbon::parse($value)->format($format);
        } catch (\Throwable) {
            return $value;
        }
    };
    $creatorName = data_get($snapshot, 'seller.company_name') ?: data_get($snapshot, 'seller.name', 'Prestador');
    $payment = data_get($snapshot, 'payment') ?: [];
@endphp

<div class="header">
    @if ($companyLogoPath)
        <img src="{{ $companyLogoPath }}" alt="{{ $creatorName }}" class="brand-logo">
    @endif
    <div class="brand">{{ $creatorName }}</div>
    <div class="meta">Proposta comercial</div>
    <h1>{{ data_get($snapshot, 'proposal.title') }}</h1>
</div>

<div class="section compact-grid">
    <div class="compact-cell">
        <div class="box">
            <h2>Cliente</h2>
            <div class="info-row">
                <span class="info-label">Nome</span>
                <span class="info-value">{{ data_get($snapshot, 'client.name', 'Não informado') }}</span>
            </div>
            @if (data_get($snapshot, 'client.company'))
                <div class="info-row">
                    <span class="info-label">Empresa</span>
                    <span>{{ data_get($snapshot, 'client.company') }}</span>
                </div>
            @endif
            @if (data_get($snapshot, 'client.email'))
                <div class="info-row">
                    <span class="info-label">E-mail</span>
                    <span>{{ data_get($snapshot, 'client.email') }}</span>
                </div>
            @endif
            @if (data_get($snapshot, 'client.phone'))
                <div class="info-row">
                    <span class="info-label">Telefone</span>
                    <span>{{ data_get($snapshot, 'client.phone') }}</span>
                </div>
            @endif
        </div>
    </div>
    <div class="compact-cell">
        <div class="box">
            <h2>Dados da proposta</h2>
            <div class="info-row">
                <span class="info-label">Validade</span>
                <span class="info-value">{{ $date(data_get($snapshot, 'proposal.valid_until')) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Prazo</span>
                <span class="info-value">{{ data_get($snapshot, 'proposal.delivery_estimate') ?: 'Não informado' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Aceita em</span>
                <span class="info-value">{{ $date(data_get($snapshot, 'acceptance.accepted_at'), 'd/m/Y H:i') }}</span>
            </div>
        </div>
    </div>
</div>

<div class="section">
    <h2>Introdução</h2>
    <div class="box note">{{ trim(data_get($snapshot, 'proposal.introduction') ?: 'Sem descrição informada.') }}</div>
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
            @foreach (data_get($snapshot, 'items', []) as $item)
                <tr>
                    <td>{{ $item['description'] ?? '' }}</td>
                    <td class="right">{{ number_format((float) ($item['quantity'] ?? 0), 2, ',', '.') }}</td>
                    <td class="right">{{ $money($item['unit_price'] ?? 0) }}</td>
                    <td class="right"><strong>{{ $money($item['subtotal'] ?? 0) }}</strong></td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="total-line">
            <div class="total-item">
                <span class="total-label">Subtotal:</span>
                <span class="total-value">{{ $money(data_get($snapshot, 'proposal.subtotal')) }}</span>
            </div>
            <div class="total-item">
                <span class="total-label">Desconto:</span>
                <span class="total-value">{{ $money(data_get($snapshot, 'proposal.manual_discount')) }}</span>
            </div>
            <div class="total-item">
                <span class="total-label">Total:</span>
                <span class="total-value total-final">{{ $money(data_get($snapshot, 'proposal.total')) }}</span>
            </div>
        </div>
    </div>
</div>

@if ($payment)
    <div class="section">
        <div class="box">
            <h2>Pagamento</h2>
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
                        <span>({{ $payment['pix_key_type_label'] }})</span>
                    @endif
                </div>
            @endif
            @if ($payment['link'] ?? null)
                <div class="info-row">
                    <span class="info-label">Link</span>
                    <span>{{ $payment['link'] }}</span>
                </div>
            @endif
            @if ($payment['instructions'] ?? null)
                <div style="margin-top: 6px;">
                    <div class="label">Instruções de pagamento</div>
                    <div class="note">{{ $payment['instructions'] }}</div>
                </div>
            @endif
        </div>
    </div>
@endif

<div class="section">
    <div class="box">
        <h2>Aceite da proposta</h2>
        <div class="info-row">
            <span class="info-label">Responsável</span>
            <span class="info-value">{{ data_get($snapshot, 'acceptance.accepted_by_name') ?: 'Não registrado' }}</span>
        </div>
        @if (data_get($snapshot, 'acceptance.accepted_by_email'))
            <div class="info-row">
                <span class="info-label">E-mail</span>
                <span>{{ data_get($snapshot, 'acceptance.accepted_by_email') }}</span>
            </div>
        @endif
        @if (data_get($snapshot, 'acceptance.acceptance_notes'))
            <div style="margin-top: 6px;">
                <div class="label">Observações do cliente</div>
                <div class="note">{{ data_get($snapshot, 'acceptance.acceptance_notes') }}</div>
            </div>
        @endif
    </div>
</div>

@if ($cancellation)
    <div class="section">
        <div class="box">
            <h2>Cancelamento</h2>
            <div class="info-row">
                <span class="info-label">Cancelada em</span>
                <span class="info-value">{{ $date(data_get($cancellation, 'canceled_at'), 'd/m/Y H:i') }}</span>
            </div>
            @if (data_get($cancellation, 'canceled_reason'))
                <div style="margin-top: 6px;">
                    <div class="label">Motivo</div>
                    <div class="note">{{ data_get($cancellation, 'canceled_reason') }}</div>
                </div>
            @endif
        </div>
    </div>
@endif

<div class="footer">
    PDF gerado pelo propostei. Observações internas e IP de aceite não são incluídos neste documento.
</div>
</body>
</html>
