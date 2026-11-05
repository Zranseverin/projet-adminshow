<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Order;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $period = in_array($request->string('period')->toString(), ['week', 'month', 'year'], true)
            ? $request->string('period')->toString()
            : 'month';

        [$start, $end, $periodLabel] = $this->periodBounds($period);
        $previousEnd = $start->subSecond();
        $previousStart = $previousEnd->subSeconds($end->diffInSeconds($start));

        $orders = Order::whereBetween('created_at', [$start, $end])->get();
        $previousOrders = Order::whereBetween('created_at', [$previousStart, $previousEnd])->get();
        $paidOrders = $orders->where('payment_status', 'paid');
        $deliveredStatuses = ['delivered', 'completed', 'livree', 'livrée'];
        $cancelledStatuses = ['cancelled', 'canceled', 'annulee', 'annulée'];

        $deliveredOrders = $orders->filter(fn (Order $order) => in_array(strtolower($order->status), $deliveredStatuses, true));
        $cancelledOrders = $orders->filter(fn (Order $order) => in_array(strtolower($order->status), $cancelledStatuses, true));
        $inProgressOrders = $orders->reject(fn (Order $order) => $deliveredOrders->contains('id', $order->id) || $cancelledOrders->contains('id', $order->id));

        $revenue = (float) $paidOrders->sum('total');
        $previousRevenue = (float) $previousOrders->where('payment_status', 'paid')->sum('total');
        $uniqueCustomers = $orders->unique(fn (Order $order) => $order->customer_email ?: ($order->customer_phone ?: $order->customer_name))->count();
        $previousCustomers = $previousOrders->unique(fn (Order $order) => $order->customer_email ?: ($order->customer_phone ?: $order->customer_name))->count();

        $recentOrders = Order::query()
            ->when($request->filled('q'), function (Builder $query) use ($request): void {
                $search = $request->string('q')->toString();
                $query->where(function (Builder $query) use ($search): void {
                    $query->where('reference', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%")
                        ->orWhere('customer_email', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->limit(5)
            ->get();

        $paymentChannels = $orders
            ->groupBy(fn (Order $order) => $order->payment_method ?: 'Non renseigné')
            ->map(fn ($channelOrders, $name) => [
                'name' => $name,
                'count' => $channelOrders->count(),
                'total' => (float) $channelOrders->where('payment_status', 'paid')->sum('total'),
            ])
            ->sortByDesc('total')
            ->values()
            ->take(3);

        return view('dashboard.index', [
            'period' => $period,
            'periodLabel' => $periodLabel,
            'start' => $start,
            'end' => $end,
            'bookCount' => Book::count(),
            'newBookCount' => Book::whereBetween('created_at', [$start, $end])->count(),
            'orderCount' => $orders->count(),
            'orderGrowth' => $this->growth($orders->count(), $previousOrders->count()),
            'revenue' => $revenue,
            'revenueGrowth' => $this->growth($revenue, $previousRevenue),
            'customerCount' => $uniqueCustomers,
            'customerGrowth' => $this->growth($uniqueCustomers, $previousCustomers),
            'lowStockCount' => Book::where('stock_quantity', '<', 10)->count(),
            'recentOrders' => $recentOrders,
            'deliveredCount' => $deliveredOrders->count(),
            'inProgressCount' => $inProgressOrders->count(),
            'cancelledCount' => $cancelledOrders->count(),
            'deliveryRate' => $orders->isEmpty() ? 0 : round($deliveredOrders->count() / $orders->count() * 100),
            'paidRate' => $orders->isEmpty() ? 0 : round($paidOrders->count() / $orders->count() * 100, 1),
            'averageBasket' => $paidOrders->isEmpty() ? 0 : (float) $paidOrders->avg('total'),
            'averageRating' => (float) (Book::where('review_count', '>', 0)->avg('rating') ?? 0),
            'shippingTotal' => (float) $orders->sum('shipping_total'),
            'discountTotal' => (float) $orders->sum('discount_total'),
            'pendingTotal' => (float) $orders->where('payment_status', '!=', 'paid')->sum('total'),
            'paymentChannels' => $paymentChannels,
            'lowStockBooks' => Book::with('author')->where('stock_quantity', '<', 10)->orderBy('stock_quantity')->limit(4)->get(),
        ]);
    }

    private function periodBounds(string $period): array
    {
        $now = CarbonImmutable::now();

        return match ($period) {
            'week' => [$now->startOfWeek(), $now->endOfWeek(), 'Cette semaine'],
            'year' => [$now->startOfYear(), $now->endOfYear(), 'Cette année'],
            default => [$now->startOfMonth(), $now->endOfMonth(), 'Ce mois'],
        };
    }

    private function growth(float|int $current, float|int $previous): float
    {
        if ((float) $previous === 0.0) {
            return (float) $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }
}