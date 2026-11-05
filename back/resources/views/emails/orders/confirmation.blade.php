<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Confirmation de commande</title>
</head>
<body style="margin:0;background:#f4f6f9;color:#1c2b3a;font-family:Arial,sans-serif">
    <div style="max-width:680px;margin:0 auto;padding:24px">
        <div style="background:#ffffff;border:1px solid #ddd8d0;border-radius:8px;padding:24px">
            <h1 style="margin:0 0 8px;font-size:24px">Commande recue</h1>
            <p style="margin:0 0 18px;color:#516070">Bonjour {{ $order->customer_name }}, votre commande {{ $order->reference }} a bien ete enregistree.</p>

            <table style="width:100%;border-collapse:collapse;margin:18px 0">
                <thead>
                    <tr>
                        <th style="text-align:left;border-bottom:1px solid #ddd8d0;padding:8px">Livre</th>
                        <th style="text-align:left;border-bottom:1px solid #ddd8d0;padding:8px">Format</th>
                        <th style="text-align:right;border-bottom:1px solid #ddd8d0;padding:8px">Qté</th>
                        <th style="text-align:right;border-bottom:1px solid #ddd8d0;padding:8px">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($order->items as $item)
                        <tr>
                            <td style="padding:8px;border-bottom:1px solid #f1eee8">{{ $item->book_title }}</td>
                            <td style="padding:8px;border-bottom:1px solid #f1eee8">{{ $item->format }}</td>
                            <td style="padding:8px;border-bottom:1px solid #f1eee8;text-align:right">{{ $item->quantity }}</td>
                            <td style="padding:8px;border-bottom:1px solid #f1eee8;text-align:right">{{ number_format((float) $item->total_price, 0, ',', ' ') }} FCFA</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <p style="margin:8px 0">Sous-total : <strong>{{ number_format((float) $order->subtotal, 0, ',', ' ') }} FCFA</strong></p>
            <p style="margin:8px 0">Frais de livraison : <strong>{{ number_format((float) $order->shipping_total, 0, ',', ' ') }} FCFA</strong></p>
            <p style="margin:8px 0;font-size:18px">Total : <strong>{{ number_format((float) $order->total, 0, ',', ' ') }} FCFA</strong></p>
            <p style="margin:18px 0 0;color:#516070">Vous recevrez un recu de paiement par email des que le paiement sera confirme.</p>
        </div>
    </div>
</body>
</html>
