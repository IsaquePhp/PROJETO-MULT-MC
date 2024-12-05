<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now();
        $startOfMonth = $now->startOfMonth();
        $endOfMonth = $now->copy()->endOfMonth();

        // Métricas principais
        $pageViews = rand(10000, 15000); // Simulado - Você pode integrar com Google Analytics
        $pageViewsGrowth = 15.85;
        
        $totalRevenue = Sale::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->sum('total_amount');
        $lastMonthRevenue = Sale::whereBetween('created_at', [
            $startOfMonth->copy()->subMonth(),
            $endOfMonth->copy()->subMonth()
        ])->sum('total_amount');
        $revenueGrowth = $lastMonthRevenue > 0 
            ? (($totalRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 
            : 0;

        $bounceRate = 86.5; // Simulado - Você pode integrar com Google Analytics
        $bounceRateChange = -24.25;

        // Dados de vendas por mês
        $salesData = Sale::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(total_amount) as total'),
            'country'
        )
            ->whereDate('created_at', '>=', $now->copy()->subMonths(3))
            ->groupBy('month', 'country')
            ->get()
            ->groupBy('month');

        // Distribuição de vendas por canal
        $salesDistribution = [
            'website' => Sale::where('channel', 'website')->sum('total_amount'),
            'mobile_app' => Sale::where('channel', 'mobile_app')->sum('total_amount'),
            'others' => Sale::whereNotIn('channel', ['website', 'mobile_app'])->sum('total_amount')
        ];

        // Integrações (simulado - você pode personalizar conforme suas integrações reais)
        $integrations = [
            [
                'id' => 1,
                'name' => 'Stripe',
                'type' => 'Finance',
                'rate' => 40,
                'profit' => 650.00
            ],
            [
                'id' => 2,
                'name' => 'Zapier',
                'type' => 'CRM',
                'rate' => 80,
                'profit' => 720.50
            ],
            [
                'id' => 3,
                'name' => 'Shopify',
                'type' => 'Marketplace',
                'rate' => 20,
                'profit' => 432.25
            ]
        ];

        return response()->json([
            'metrics' => [
                'pageViews' => $pageViews,
                'pageViewsGrowth' => $pageViewsGrowth,
                'totalRevenue' => $totalRevenue,
                'revenueGrowth' => $revenueGrowth,
                'bounceRate' => $bounceRate,
                'bounceRateChange' => $bounceRateChange
            ],
            'salesData' => $salesData,
            'salesDistribution' => $salesDistribution,
            'integrations' => $integrations
        ]);
    }
}
