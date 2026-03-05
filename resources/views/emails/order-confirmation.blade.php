<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; color: #333; max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #111; color: #fff; padding: 20px; text-align: center; border-radius: 8px; }
        .header h1 { margin: 0; font-size: 24px; }
        .content { padding: 20px 0; }
        .order-info { background: #f9f9f9; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .footer { text-align: center; color: #999; font-size: 12px; margin-top: 30px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>ARVA</h1>
        <p>Paldies par pasūtījumu!</p>
    </div>

    <div class="content">
        <p>Sveiki, <strong>{{ $order->full_name }}</strong>!</p>
        <p>Jūsu pasūtījums <strong>#{{ $order->id }}</strong> ir saņemts un tiek apstrādāts.</p>

        <div class="order-info">
            <p><strong>Pasūtījuma summa:</strong> €{{ number_format($order->total, 2) }}</p>
            <p><strong>Piegādes adrese:</strong> {{ $order->address }}, {{ $order->city }}, {{ $order->postcode }}, {{ $order->country }}</p>
        </div>

        <p>Kvīts ir pievienota šim epastam kā PDF fails.</p>
        <p>Paldies, ka izvēlējāties ARVA! 🛍️</p>
    </div>

    <div class="footer">
        <p>© {{ date('Y') }} ARVA. Visas tiesības aizsargātas.</p>
    </div>
</body>
</html>