<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'DejaVu Sans', sans-serif; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #072536; padding: 40px; font-size: 13px; background: #fff; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 3px solid #13c4ab;
        }

        .logo img {
            height: 60px;
            width: auto;
        }

        .receipt-info {
            text-align: right;
        }

        .receipt-title {
            font-size: 20px;
            font-weight: 900;
            color: #072536;
        }

        .receipt-num {
            font-size: 14px;
            color: #13c4ab;
            font-weight: 700;
            margin-top: 4px;
        }

        .receipt-date {
            font-size: 11px;
            color: #666;
            margin-top: 2px;
        }

        .status-badge {
            display: inline-block;
            background: #13c4ab;
            color: #fff;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            margin-top: 6px;
        }

        .info-grid {
            width: 100%;
            margin: 20px 0;
        }

        .info-grid td {
            width: 50%;
            vertical-align: top;
            padding: 0 10px 0 0;
            border: none;
            background: none;
        }

        .section-title {
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #13c4ab;
            margin-bottom: 8px;
            padding-bottom: 4px;
            border-bottom: 1px solid #e0f7f4;
        }

        .info-box {
            background: #f7fbfc;
            border: 1px solid #e0f7f4;
            border-radius: 8px;
            padding: 12px;
            font-size: 12px;
            line-height: 1.8;
            color: #072536;
        }

        .items-section {
            margin: 20px 0;
        }

        table.items {
            width: 100%;
            border-collapse: collapse;
        }

        table.items th {
            background: #072536;
            color: #fff;
            padding: 10px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        table.items td {
            padding: 10px 12px;
            border-bottom: 1px solid #e0f7f4;
            font-size: 12px;
            color: #072536;
            background: #fff;
        }

        table.items tr:nth-child(even) td {
            background: #f7fbfc;
        }

        table.items .total-row td {
            font-weight: 900;
            font-size: 14px;
            background: #072536;
            color: #fff;
            border: none;
        }

        table.items .total-row td.accent {
            color: #13c4ab;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            color: #999;
            font-size: 11px;
            border-top: 1px solid #e0f7f4;
            padding-top: 15px;
        }

        .footer span {
            color: #13c4ab;
            font-weight: 700;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="logo">
            <img src="{{ public_path('bildites/Logo_Arva.png') }}" alt="ARVA" style="height:55px;width:auto;" />
        </div>
        <div class="receipt-info">
            <div class="receipt-title">Pasūtījuma kvīts</div>
            <div class="receipt-num">#{{ $order->id }}</div>
            <div class="receipt-date">{{ $order->created_at->format('d.m.Y H:i') }}</div>
            <div><span class="status-badge">{{ ucfirst($order->status) }}</span></div>
        </div>
    </div>

    <table class="info-grid">
        <tr>
            <td>
                <div class="section-title">Piegādes adrese</div>
                <div class="info-box">
                    <strong>{{ $order->full_name }}</strong><br>
                    {{ $order->address }}<br>
                    {{ $order->city }}, {{ $order->postcode }}<br>
                    {{ $order->country }}
                </div>
            </td>
            <td>
                <div class="section-title">Kontaktinformācija</div>
                <div class="info-box">
                    {{ $order->email }}<br>
                    @if($order->phone)
                        {{ $order->phone }}
                    @endif
                </div>
            </td>
        </tr>
    </table>

    <div class="items-section">
        <div class="section-title">Pasūtītās preces</div>
        <table class="items">
            <thead>
                <tr>
                    <th>Prece</th>
                    <th>Krāsa</th>
                    <th>Izmērs</th>
                    <th>Cena</th>
                    <th>Daudzums</th>
                    <th>Summa</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->color ?? '—' }}</td>
                    <td>{{ $item->size ?? '—' }}</td>
                    <td>€{{ number_format($item->price, 2) }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>€{{ number_format($item->price * $item->qty, 2) }}</td>
                </tr>
                @endforeach
                <tr class="total-row">
                    <td colspan="5">Kopā</td>
                    <td class="accent">€{{ number_format($order->total, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        <p>Paldies, ka izvēlējāties <span>ARVA</span>! &nbsp;•&nbsp; © {{ date('Y') }} ARVA. Visas tiesības aizsargātas.</p>
    </div>

</body>
</html>