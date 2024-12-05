<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CashFlow;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CashFlowController extends Controller
{
    public function index(Request $request)
    {
        $query = CashFlow::query();
        
        if ($request->has('store_id')) {
            $query->where('store_id', $request->store_id);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $transactions = $query->with(['store', 'user'])
                            ->latest()
                            ->paginate(10);

        return response()->json($transactions);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'description' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $transaction = CashFlow::create([
            'store_id' => $request->store_id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'description' => $request->description,
            'notes' => $request->notes
        ]);

        return response()->json($transaction, 201);
    }

    public function show(CashFlow $cashFlow)
    {
        $cashFlow->load(['store', 'user']);
        return response()->json($cashFlow);
    }

    public function balance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $query = CashFlow::where('store_id', $request->store_id);

        if ($request->has('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        $balance = [
            'income' => $query->clone()->where('type', 'income')->sum('amount'),
            'expense' => $query->clone()->where('type', 'expense')->sum('amount')
        ];

        $balance['total'] = $balance['income'] - $balance['expense'];

        return response()->json($balance);
    }

    public function report(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'store_id' => 'required|exists:stores,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $store = Store::findOrFail($request->store_id);
        $query = CashFlow::where('store_id', $store->id)
            ->whereBetween('created_at', [$request->start_date, $request->end_date]);

        $report = [
            'store' => $store->name,
            'period' => [
                'start' => $request->start_date,
                'end' => $request->end_date
            ],
            'summary' => [
                'income' => $query->clone()->where('type', 'income')->sum('amount'),
                'expense' => $query->clone()->where('type', 'expense')->sum('amount')
            ],
            'by_payment_method' => DB::table('cash_flow')
                ->select('payment_method', 'type', DB::raw('SUM(amount) as total'))
                ->where('store_id', $store->id)
                ->whereBetween('created_at', [$request->start_date, $request->end_date])
                ->groupBy('payment_method', 'type')
                ->get()
                ->groupBy('payment_method')
                ->map(function ($items) {
                    return [
                        'income' => $items->where('type', 'income')->sum('total'),
                        'expense' => $items->where('type', 'expense')->sum('total')
                    ];
                }),
            'daily_totals' => DB::table('cash_flow')
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    'type',
                    DB::raw('SUM(amount) as total')
                )
                ->where('store_id', $store->id)
                ->whereBetween('created_at', [$request->start_date, $request->end_date])
                ->groupBy('date', 'type')
                ->orderBy('date')
                ->get()
                ->groupBy('date')
                ->map(function ($items) {
                    return [
                        'income' => $items->where('type', 'income')->sum('total'),
                        'expense' => $items->where('type', 'expense')->sum('total'),
                        'balance' => $items->where('type', 'income')->sum('total') - 
                                   $items->where('type', 'expense')->sum('total')
                    ];
                })
        ];

        $report['summary']['balance'] = $report['summary']['income'] - $report['summary']['expense'];

        return response()->json($report);
    }
}
