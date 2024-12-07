<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use App\Models\Product;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard data
     */
    public function index(Request $request)
    {
        try {
            // Definir período
            $startDate = $request->input('start_date', Carbon::now()->startOfMonth());
            $endDate = $request->input('end_date', Carbon::now()->endOfDay());
            $previousStartDate = Carbon::parse($startDate)->subPeriod(Carbon::parse($startDate), Carbon::parse($endDate));

            // Métricas do período atual
            $currentMetrics = $this->getPeriodMetrics($startDate, $endDate);
            
            // Métricas do período anterior (para calcular crescimento)
            $previousMetrics = $this->getPeriodMetrics($previousStartDate, Carbon::parse($startDate)->subDay());

            // Calcular crescimento
            $growth = $this->calculateGrowth($currentMetrics, $previousMetrics);

            // Dados para gráficos
            $salesByDay = $this->getSalesByDay($startDate, $endDate);
            $topProducts = $this->getTopProducts($startDate, $endDate);
            $topClients = $this->getTopClients($startDate, $endDate);
            $salesByStatus = $this->getSalesByStatus($startDate, $endDate);

            return response()->json([
                'success' => true,
                'data' => [
                    'metrics' => [
                        'current' => $currentMetrics,
                        'growth' => $growth
                    ],
                    'charts' => [
                        'salesByDay' => $salesByDay,
                        'topProducts' => $topProducts,
                        'topClients' => $topClients,
                        'salesByStatus' => $salesByStatus
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erro ao carregar dados do dashboard',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get metrics for a specific period
     */
    private function getPeriodMetrics($startDate, $endDate)
    {
        // Total de vendas
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate]);
        $totalSales = $sales->count();
        $totalRevenue = $sales->sum('total');

        // Média de vendas por dia
        $days = max(1, Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1);
        $averageDailySales = $totalSales / $days;

        // Ticket médio
        $averageTicket = $totalSales > 0 ? $totalRevenue / $totalSales : 0;

        // Produtos mais vendidos
        $topProducts = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();

        return [
            'totalSales' => $totalSales,
            'totalRevenue' => $totalRevenue,
            'averageDailySales' => $averageDailySales,
            'averageTicket' => $averageTicket,
            'topProducts' => $topProducts
        ];
    }

    /**
     * Calculate growth percentages
     */
    private function calculateGrowth($current, $previous)
    {
        $calculatePercentage = function($current, $previous) {
            if ($previous == 0) return $current > 0 ? 100 : 0;
            return round((($current - $previous) / $previous) * 100, 2);
        };

        return [
            'sales' => $calculatePercentage($current['totalSales'], $previous['totalSales']),
            'revenue' => $calculatePercentage($current['totalRevenue'], $previous['totalRevenue']),
            'averageTicket' => $calculatePercentage($current['averageTicket'], $previous['averageTicket'])
        ];
    }

    /**
     * Get sales data grouped by day
     */
    private function getSalesByDay($startDate, $endDate)
    {
        return Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total_sales'),
                DB::raw('SUM(total) as total_revenue')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    /**
     * Get top selling products
     */
    private function getTopProducts($startDate, $endDate)
    {
        return DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->select(
                'products.name',
                DB::raw('SUM(sale_items.quantity) as total_quantity'),
                DB::raw('SUM(sale_items.quantity * sale_items.price) as total_revenue')
            )
            ->groupBy('products.id', 'products.name')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    /**
     * Get top clients
     */
    private function getTopClients($startDate, $endDate)
    {
        return Sale::whereBetween('created_at', [$startDate, $endDate])
            ->join('clients', 'sales.client_id', '=', 'clients.id')
            ->select(
                'clients.name',
                DB::raw('COUNT(*) as total_purchases'),
                DB::raw('SUM(sales.total) as total_spent')
            )
            ->groupBy('clients.id', 'clients.name')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->get();
    }

    /**
     * Get sales by status
     */
    private function getSalesByStatus($startDate, $endDate)
    {
        return Sale::whereBetween('created_at', [$startDate, $endDate])
            ->select('sale_status', DB::raw('COUNT(*) as total'))
            ->groupBy('sale_status')
            ->get();
    }
}
