<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreOrderRequest;
use App\Http\Resources\OrderResource;
use App\Models\Book;
use App\Models\DeliveryOption;
use App\Models\Order;
use App\Models\StockMovement;
use App\Support\OrderMailer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class OrderCheckoutController extends Controller
{
    public function __construct(private OrderMailer $orderMailer)
    {
    }

    public function store(StoreOrderRequest $request): OrderResource|JsonResponse
    {
        $data = $request->validated();

        try {
            $order = DB::transaction(function () use ($data): Order {
                $deliveryOption = DeliveryOption::where('code', $data['shipping']['method'] ?? 'pickup')
                    ->where('is_active', true)
                    ->first();
                abort_unless($deliveryOption, 422, 'Mode de livraison invalide.');

                $shippingTotal = (float) $deliveryOption->price;
                $paymentMethod = $data['payment']['method'];
                $isCash = $paymentMethod === 'cash';
                $subtotal = 0;

                $order = Order::create([
                    'reference' => $this->reference(),
                    'customer_name' => trim($data['customer']['first_name'].' '.$data['customer']['last_name']),
                    'customer_email' => $data['customer']['email'] ?? null,
                    'customer_phone' => $data['customer']['phone'] ?? null,
                    'shipping_address' => trim($data['shipping']['address'].' '.($data['shipping']['address_extra'] ?? '')),
                    'shipping_city' => trim(($data['shipping']['postal_code'] ?? '').' '.$data['shipping']['city']),
                    'shipping_country' => $data['shipping']['country'],
                    'status' => $isCash ? 'pending' : 'paid',
                    'payment_status' => $isCash ? 'pending' : 'paid',
                    'payment_method' => $paymentMethod,
                    'payment_reference' => $data['payment']['reference'] ?? $this->paymentReference(),
                    'paid_at' => $isCash ? null : now(),
                    'subtotal' => 0,
                    'discount_total' => 0,
                    'shipping_total' => $shippingTotal,
                    'total' => 0,
                    'notes' => 'Commande creee depuis le front.',
                ]);

                foreach ($data['items'] as $item) {
                    $book = Book::query()->lockForUpdate()->with('author')->findOrFail($item['book_id']);
                    $quantity = (int) $item['quantity'];

                    if (! $book->is_active) {
                        abort(422, 'Le livre "'.$book->title.'" n\'est plus disponible.');
                    }

                    if ($book->stock_quantity < $quantity) {
                        abort(422, 'Stock insuffisant pour "'.$book->title.'".');
                    }

                    $selectedFormat = $item['format'] ?? $book->format;
                    $unitPrice = $this->priceForFormat($book, $selectedFormat);
                    $lineTotal = $unitPrice * $quantity;
                    $subtotal += $lineTotal;
                    $stockBefore = (int) $book->stock_quantity;
                    $stockAfter = $stockBefore - $quantity;

                    $order->items()->create([
                        'book_id' => $book->id,
                        'book_title' => $book->title,
                        'author_name' => $book->author?->name,
                        'format' => $selectedFormat,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'total_price' => $lineTotal,
                    ]);

                    $book->update(['stock_quantity' => $stockAfter]);

                    StockMovement::create([
                        'book_id' => $book->id,
                        'type' => 'sale',
                        'quantity' => -$quantity,
                        'stock_before' => $stockBefore,
                        'stock_after' => $stockAfter,
                        'source' => $order->reference,
                        'note' => 'Commande client depuis le site.',
                    ]);
                }

                $order->update([
                    'subtotal' => $subtotal,
                    'total' => $subtotal + $shippingTotal,
                ]);

                return $order->load('items.book');
            });
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $exception) {
            return response()->json(['message' => $exception->getMessage()], $exception->getStatusCode());
        }

        $this->orderMailer->sendConfirmation($order);
        $this->orderMailer->sendReceipt($order);

        return new OrderResource($order);
    }

    private function reference(): string
    {
        return 'CMD-'.now()->format('Ymd-His').'-'.strtoupper(str()->random(4));
    }

    private function paymentReference(): string
    {
        return 'PAY-'.now()->format('YmdHis').'-'.strtoupper(str()->random(5));
    }

    private function priceForFormat(Book $book, string $formatName): float
    {
        $format = collect($book->book_formats ?: [])
            ->first(fn ($format): bool => ($format['name'] ?? null) === $formatName);

        return (float) ($format['price'] ?? $book->price);
    }
}
