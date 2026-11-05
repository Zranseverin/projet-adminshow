<?php

namespace App\Http\Controllers\Marketplace;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SellerOrder;
use App\Models\Seller;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class SellerDashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        return $this->render($request, $request->user()->seller);
    }

    public function showForAdmin(Request $request, Seller $seller)
    {
        return $this->render($request, $seller);
    }

    private function render(Request $request, Seller $seller)
    {
        $adminPreview = $request->user()->account_type === 'admin';
        return view('marketplace.seller-dashboard-static', compact('seller', 'adminPreview'));
    }

    // Retained for the future connection of the approved dashboard design.
    private function renderDynamic(Request $request, Seller $seller)
    {
        $data = $request->validate(['period' => 'nullable|in:7,30,90']);
        $days = (int) ($data['period'] ?? 30);
        $start = now()->startOfDay()->subDays($days - 1);
        $end = now()->endOfDay();
        $orders = SellerOrder::where('seller_id', $seller->id);
        $paid = (clone $orders)->where('payment_status', 'paid')->where('status', '!=', 'cancelled')->whereBetween('paid_at', [$start, $end]);
        $products = Product::where('seller_id', $seller->id);
        $stats = [
            'revenue' => (clone $paid)->sum('seller_due_minor'),
            'sales' => (clone $paid)->count(),
            'orders' => (clone $orders)->whereBetween('created_at', [$start, $end])->count(),
            'to_process' => (clone $orders)->whereIn('status', ['pending', 'processing'])->count(),
            'products' => (clone $products)->count(),
            'published' => (clone $products)->where('status', 'published')->count(),
            'review' => (clone $products)->where('status', 'pending')->count(),
            'units' => (clone $products)->sum('stock'),
            'low' => (clone $products)->whereBetween('stock', [1, 5])->count(),
            'out' => (clone $products)->where('stock', 0)->count(),
        ];
        $daily = (clone $paid)->selectRaw('DATE(paid_at) as day, SUM(seller_due_minor) as amount')->groupByRaw('DATE(paid_at)')->pluck('amount', 'day');
        $series = []; $cumulative = 0;
        foreach (CarbonPeriod::create($start, $end->copy()->startOfDay()) as $date) {
            $amount = (int) ($daily[$date->format('Y-m-d')] ?? 0);
            $cumulative += $amount;
            $series[] = ['date' => $date->format('d/m'), 'amount' => $amount, 'cumulative' => $cumulative];
        }
        $recentOrders = (clone $orders)->with('marketOrder')->latest()->limit(5)->get();
        $recentProducts = (clone $products)->latest('id')->limit(6)->get();
        $adminPreview = $request->user()->account_type === 'admin';
        return view('marketplace.seller-dashboard', compact('seller', 'days', 'stats', 'series', 'recentOrders', 'recentProducts', 'adminPreview'));
    }
}
