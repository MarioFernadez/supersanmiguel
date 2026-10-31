<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $sale->isElectronicInvoice() ? 'KuDE #' . ($sale->sifen_numero_factura ?? $sale->invoice_number) : 'Ticket #' . $sale->invoice_number }}</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            margin: 0;
            padding: 5px;
            width: 80mm; /* Standard thermal paper width */
            background-color: #fff;
            color: #000;
        }
        .header, .footer {
            text-align: center;
            margin-bottom: 8px;
        }
        .store-name {
            font-size: 15px;
            font-weight: bold;
        }
        .banner-test {
            border: 2px dashed #000;
            padding: 4px;
            font-weight: bold;
            font-size: 9.5px;
            text-align: center;
            margin-bottom: 6px;
            line-height: 1.15;
        }
        .info {
            margin-bottom: 8px;
            font-size: 10.5px;
            line-height: 1.3;
        }
        .items {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
            font-size: 10.5px;
        }
        .items th {
            text-align: left;
            border-bottom: 1px dashed black;
            padding: 2px 0;
        }
        .items td {
            padding: 2px 0;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            margin-top: 8px;
            border-top: 1px dashed black;
            padding-top: 5px;
            font-size: 10.5px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            font-size: 13px;
            margin: 3px 0;
        }
        .row-between {
            display: flex;
            justify-content: space-between;
        }
        .qr-section {
            text-align: center;
            margin-top: 8px;
            padding-top: 6px;
            border-top: 1px dashed black;
        }
        .qr-container {
            display: flex;
            justify-content: center;
            margin: 6px 0;
        }
        .qr-container svg {
            width: 125px;
            height: 125px;
        }
        .cdc-text {
            font-size: 8px;
            font-weight: bold;
            word-break: break-all;
            background: #f0f0f0;
            padding: 3px;
            border: 1px solid #000;
            margin: 4px 0;
        }
        .legal-notice {
            font-size: 7.5px;
            line-height: 1.2;
            color: #333;
            margin-top: 4px;
        }
        @media print {
            body {
                width: auto;
                margin: 0;
                padding: 2px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>

    @if($sale->isElectronicInvoice())
        {{-- MODO KuDE FACTURA ELECTRÓNICA SIFEN --}}
        @if($sale->sifen_estado === 'aprobado_test')
        <div class="banner-test">
            *** AMBIENTE DE PRUEBA ***<br>
            DOCUMENTO ELECTRÓNICO SIN VALOR COMERCIAL NI FISCAL
        </div>
        @endif

        <div class="header">
            <div style="font-size: 12px; font-weight: bold;">KuDE DE FACTURA ELECTRÓNICA</div>
            <div class="store-name">{{ $settings['sifen_emisor_razon_social'] ?? ($settings['store_name'] ?? 'ZonaShop') }}</div>
            <div>RUC: {{ $settings['sifen_emisor_ruc'] ?? '44444401' }}-{{ $settings['sifen_emisor_dv'] ?? '7' }}</div>
            <div>{{ $settings['sifen_emisor_direccion'] ?? ($settings['store_address'] ?? '') }}</div>
            <div>Tel: {{ $settings['sifen_emisor_telefono'] ?? ($settings['store_phone'] ?? '') }}</div>
        </div>

        <div class="info">
            <div><strong>Timbrado N°:</strong> {{ $settings['sifen_timbrado'] ?? '12345678' }}</div>
            <div><strong>Factura N°:</strong> {{ $sale->sifen_numero_factura ?? $sale->invoice_number }}</div>
            <div><strong>Fecha:</strong> {{ $sale->created_at->format('d/m/Y H:i:s') }}</div>
            <div><strong>Condición:</strong> {{ strtoupper($sale->payment_condition) }}</div>
            <div><strong>Cajero:</strong> {{ $sale->user->name }}</div>
            @if($sale->seller)
            <div><strong>Vendedor:</strong> {{ $sale->seller->name }}</div>
            @endif
            <div style="border-top: 1px dotted #888; margin-top: 3px; padding-top: 3px;">
                <div><strong>Cliente:</strong> {{ $sale->customer ? $sale->customer->name : 'Consumidor Final' }}</div>
                <div><strong>RUC/CI:</strong> {{ $sale->customer ? $sale->customer->ruc : '0' }}</div>
            </div>
        </div>

    @else
        {{-- MODO TICKET INTERNO ESTÁNDAR --}}
        <div class="header">
            <div class="store-name">{{ $settings['store_name'] ?? 'ZonaPOS' }}</div>
            <div>RUC: {{ $settings['store_ruc'] ?? '---' }}</div>
            <div>{{ $settings['store_address'] ?? '' }}</div>
            <div>Tel: {{ $settings['store_phone'] ?? '' }}</div>
        </div>

        <div class="info">
            <div>Ticket #: {{ $sale->invoice_number }}</div>
            <div>Fecha: {{ $sale->created_at->format('d/m/Y H:i') }}</div>
            <div>Cajero: {{ $sale->user->name }}</div>
            @if($sale->seller)
            <div>Vendedor: {{ $sale->seller->name }}</div>
            @endif
            <div>Cliente: {{ $sale->customer ? $sale->customer->name : 'Consumidor Final' }}</div>
            <div>RUC/CI: {{ $sale->customer ? $sale->customer->ruc : '---' }}</div>
            <div>Condición: {{ strtoupper($sale->payment_condition) }}</div>
        </div>
    @endif

    <table class="items">
        <thead>
            <tr>
                <th style="width: 12%">Cant</th>
                <th style="width: 48%">Prod</th>
                <th style="width: 20%" class="text-right">Precio</th>
                <th style="width: 20%" class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $item)
            <tr>
                <td>
                    {{ (float)$item->quantity == (int)$item->quantity ? (int)$item->quantity : number_format($item->quantity, 3, ',', '.') }}@if(($item->product->unit_type ?? '') === 'kg')&nbsp;kg @endif
                </td>
                <td>{{ Str::limit($item->product_name ?: ($item->product->name ?? 'Producto'), 22) }}</td>
                <td class="text-right">{{ number_format($item->price_unit, 0, '', '.') }}</td>
                <td class="text-right">{{ number_format($item->subtotal, 0, '', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        @php
            $baseGrav10 = (int)($sale->tax_10 > 0 ? round($sale->tax_10 * 10) : ($sale->total - $sale->tax_5 * 21));
            $baseGrav5  = (int)($sale->tax_5 > 0 ? round($sale->tax_5 * 20) : 0);
            $exempt     = (int)($sale->tax_exempt ?? 0);
        @endphp
        @if($baseGrav10 > 0)
        <div class="row-between">
            <span>Subtotal Gravadas 10%:</span>
            <span>{{ format_pyg(max(0, $baseGrav10)) }}</span>
        </div>
        @endif
        @if($baseGrav5 > 0)
        <div class="row-between">
            <span>Subtotal Gravadas 5%:</span>
            <span>{{ format_pyg($baseGrav5) }}</span>
        </div>
        @endif
        @if($exempt > 0)
        <div class="row-between">
            <span>Subtotal Exentas:</span>
            <span>{{ format_pyg($exempt) }}</span>
        </div>
        @endif
        <div class="row-between" style="border-top: 1px dotted #000; padding-top: 2px; margin-top: 2px;">
            <span>Liquidación Total IVA:</span>
            <span>{{ format_pyg($sale->tax_10 + $sale->tax_5) }}</span>
        </div>
        <div class="total-row">
            <span>TOTAL:</span>
            <span>Gs. {{ format_pyg($sale->total) }}</span>
        </div>
        
        @if($sale->payments->count() > 0)
        <div style="margin-top: 6px; border-top: 1px dashed black; padding-top: 4px;">
            <div style="font-weight: bold; margin-bottom: 2px;">Pagos:</div>
            @foreach($sale->payments as $payment)
            <div class="row-between">
                <span>{{ ucfirst($payment->method) }}:</span>
                <span>Gs. {{ number_format($payment->amount, 0, '', '.') }}</span>
            </div>
            @if($payment->method === 'cheque')
                <div style="font-size: 9.5px; margin-left: 10px; margin-bottom: 2px;">
                    <div>Banco: {{ $payment->bank_name }}</div>
                    <div>Nro: {{ $payment->cheque_number }}</div>
                    <div>Fecha: {{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</div>
                </div>
            @endif
            @endforeach
        </div>
        @endif
    </div>

    @if($sale->isElectronicInvoice())
        {{-- SECCIÓN QR & CDC KUDE SIFEN --}}
        <div class="qr-section">
            @if($sale->sifen_qr_svg)
                <div class="qr-container">
                    {!! $sale->sifen_qr_svg !!}
                </div>
            @endif
            <div style="font-size: 8px;">
                Consulte la validez en:<br>
                <strong>{{ $sale->sifen_estado === 'aprobado_test' ? 'https://ekuatia.set.gov.py/consultas-test/' : 'https://ekuatia.set.gov.py/consultas/' }}</strong>
            </div>
            <div class="cdc-text">
                CDC: {{ $sale->formatted_cdc }}
            </div>
            <div class="legal-notice">
                ESTE DOCUMENTO ES UNA REPRESENTACIÓN GRÁFICA DE UN DOCUMENTO ELECTRÓNICO (XML)<br>
                Si su documento presenta algún error, podrá solicitar modificación dentro de 72 hs.
            </div>
        </div>
    @endif

    <div class="footer">
        <div style="margin-top: 6px;">{{ $settings['ticket_footer'] ?? 'Gracias por su compra' }}</div>
        <br>
        <button onclick="window.print()" class="no-print" style="padding: 8px 16px; cursor: pointer; font-weight: bold; background: #4f46e5; color: white; border: none; border-radius: 6px;">Imprimir</button>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
        window.onafterprint = function() {
            window.close();
        }
    </script>
</body>
</html>
