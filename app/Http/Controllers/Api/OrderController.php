<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Sale;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Sale::with(['customer', 'items.product'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($sale) {
                return [
                    'id' => $sale->id,
                    'client_name' => $sale->customer ? $sale->customer->name : 'Cliente não registrado',
                    'client_phone' => $sale->customer ? $sale->customer->phone : '',
                    'delivery_address' => $sale->delivery_address ?? '',
                    'total' => $sale->total,
                    'status' => $sale->status ?? 'pending',
                    'created_at' => $sale->created_at,
                    'processing_start' => $sale->processing_start,
                    'ready_at' => $sale->ready_at,
                    'delivery_start' => $sale->delivery_start,
                    'delivered_at' => $sale->delivered_at,
                    'items' => $sale->items->map(function ($item) {
                        return [
                            'id' => $item->id,
                            'product_name' => $item->product->name,
                            'quantity' => $item->quantity,
                            'price' => $item->price,
                            'notes' => $item->notes ?? ''
                        ];
                    })
                ];
            });

        return response()->json($orders);
    }

    public function updateStatus(Request $request, Sale $sale)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,ready,delivering,delivered',
            'processing_start' => 'nullable|date',
            'ready_at' => 'nullable|date',
            'delivery_start' => 'nullable|date',
            'delivered_at' => 'nullable|date'
        ]);

        $sale->update($request->all());

        return response()->json(['message' => 'Status atualizado com sucesso']);
    }
}
