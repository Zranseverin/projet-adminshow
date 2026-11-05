<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Support\OrderMailer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function __construct(private OrderMailer $orderMailer)
    {
    }

    public function index(): View
    {
        $orders = Order::withSum('items as products_paid_count', 'quantity')
            ->withCount('items')
            ->when(request('status'), fn ($query, string $status) => $query->where('status', $status))
            ->when(request('q'), function ($query, string $search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('reference', 'like', '%'.$search.'%')
                        ->orWhere('customer_name', 'like', '%'.$search.'%')
                        ->orWhere('customer_email', 'like', '%'.$search.'%');
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $orderHistory = Order::latest()->limit(8)->get();
        $deliveryHistory = Order::whereNotNull('shipping_address')
            ->latest('updated_at')
            ->limit(8)
            ->get();
        $paymentHistory = Order::whereNotNull('payment_method')
            ->latest('paid_at')
            ->latest()
            ->limit(8)
            ->get();

        return view('admin.orders.index', compact('orders', 'orderHistory', 'deliveryHistory', 'paymentHistory'));
    }

    public function create(): View
    {
        return view('admin.orders.create', ['order' => new Order()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['reference'] = $data['reference'] ?: 'CMD-'.now()->format('Ymd-His');
        $data['total'] = $this->totalFrom($data);

        $order = Order::create($data);
        $this->orderMailer->sendConfirmation($order);
        $this->orderMailer->sendReceipt($order);

        return redirect()->route('admin.orders.index')->with('success', 'Commande ajoutee avec succes.');
    }

    public function show(Order $order): View
    {
        $order->load('items.book');

        return view('admin.orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        return view('admin.orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $this->validatedData($request, $order);
        $data['reference'] = $data['reference'] ?: $order->reference;
        $data['total'] = $this->totalFrom($data);
        $wasPaid = $order->payment_status === 'paid';
        $order->update($data);
        $order->refresh();

        if (! $wasPaid && $order->payment_status === 'paid') {
            $this->orderMailer->sendReceipt($order);
        }

        return redirect()->route('admin.orders.index')->with('success', 'Commande modifiee avec succes.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Commande supprimee avec succes.');
    }

    private function validatedData(Request $request, ?Order $order = null): array
    {
        $orderId = $order?->id ?? 'NULL';

        return $request->validate([
            'reference' => ['nullable', 'string', 'max:80', 'unique:orders,reference,'.$orderId],
            'customer_name' => ['required', 'string', 'max:160'],
            'customer_email' => ['nullable', 'email', 'max:180'],
            'customer_phone' => ['nullable', 'string', 'max:60'],
            'shipping_address' => ['nullable', 'string', 'max:255'],
            'shipping_city' => ['nullable', 'string', 'max:100'],
            'shipping_country' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'string', 'max:40'],
            'payment_status' => ['required', 'string', 'max:40'],
            'payment_method' => ['nullable', 'string', 'max:80'],
            'payment_reference' => ['nullable', 'string', 'max:120'],
            'paid_at' => ['nullable', 'date'],
            'subtotal' => ['required', 'numeric', 'min:0'],
            'discount_total' => ['nullable', 'numeric', 'min:0'],
            'shipping_total' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }

    private function totalFrom(array $data): float
    {
        return max(0, (float) $data['subtotal'] - (float) ($data['discount_total'] ?? 0) + (float) ($data['shipping_total'] ?? 0));
    }
}
