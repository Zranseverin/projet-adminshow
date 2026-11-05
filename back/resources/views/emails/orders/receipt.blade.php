<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Recu de paiement</title>
</head>
<body style="margin:0;background:#f4f6f9;color:#1c2b3a;font-family:Arial,sans-serif">
    <div style="max-width:680px;margin:0 auto;padding:24px">
        <div style="background:#ffffff;border:1px solid #ddd8d0;border-radius:8px;padding:24px">
            <h1 style="margin:0 0 8px;font-size:24px">Paiement confirme</h1>
            <p style="margin:0 0 18px;color:#516070">Bonjour {{ $order->customer_name }}, voici le recu de paiement pour la commande {{ $order->reference }}.</p>

            <div style="background:#e8f4ec;border-radius:6px;padding:14px;margin-bottom:18px">
                <p style="margin:0 0 6px">Reference paiement : <strong>{{ $order->payment_reference ?: '-' }}</strong></p>
                <p style="margin:0 0 6px">Methode : <strong>{{ $order->payment_method ?: '-' }}</strong></p>
                <p style="margin:0">Date paiement : <strong>{{ $order->paid_at?->format('d/m/Y H:i') ?? now()->format('d/m/Y H:i') }}</strong></p>
            </div>

            <table style="width:100%;border-collapse:collapse;margin:18px 0">
                <thead>
                    <tr>
                        <th style="text-align:left;border-bottom:1px solid #ddd8d0;padding:8px">Livre</th>
                        <th style="text-align:right;border-bottom:1px solid #ddd8d0;padding:8px">Qté</th>
                        <th style="text-align:right;border-bottom:1px solid #ddd8d0;padding:8px">Montant</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td style="padding:8px;border-bottom:1px solid #f1eee8">{{ $item->book_title }} - {{ $item->format }}</td>
                            <td style="padding:8px;border-bottom:1px solid #f1eee8;text-align:right">{{ $item->quantity }}</td>
                            <td style="padding:8px;border-bottom:1px solid #f1eee8;text-align:right">{{ number_format((float) $item->total_price, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin:8px 0">Frais de livraison : <strong>{{ number_format((float) $order->shipping_total, 0, ',', ' ') }} FCFA</strong></p>
            <p style="margin:8px 0;font-size:20px">Total paye : <strong>{{ number_format((float) $order->total, 0, ',', ' ') }} FCFA</strong></p>
            <p style="margin:18px 0 0;color:#516070">Merci pour votre achat.</p>
        </div>
    </div>
</body>
</html>
