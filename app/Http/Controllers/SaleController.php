<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SaleController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'payment_method' => 'required|in:credit_card,debit_card,money,pix',
            'installments' => 'required|integer|min:1',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0'
        ]);

        try {
            DB::beginTransaction();

            // Gerar código único para a venda
            $saleCode = strtoupper(Str::random(8));
            while (Sale::where('code', $saleCode)->exists()) {
                $saleCode = strtoupper(Str::random(8));
            }

            // Calcular o total da venda
            $total = 0;
            foreach ($request->items as $item) {
                $total += $item['quantity'] * $item['price'];
            }

            // Criar a venda
            $sale = Sale::create([
                'code' => $saleCode,
                'customer_id' => $request->customer_id,
                'payment_method' => $request->payment_method,
                'installments' => $request->installments,
                'total' => $total,
                'status' => 'completed'
            ]);

            // Criar os itens da venda e atualizar estoque
            foreach ($request->items as $item) {
                // Criar item da venda
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price']
                ]);

                // Atualizar estoque do produto
                $product = Product::find($item['product_id']);
                if ($product->stock < $item['quantity']) {
                    throw new \Exception("Produto {$product->name} não possui estoque suficiente.");
                }
                $product->stock -= $item['quantity'];
                $product->save();
            }

            DB::commit();

            return response()->json([
                'message' => 'Venda realizada com sucesso',
                'sale' => $sale->load('items.product', 'customer')
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'message' => 'Erro ao realizar venda',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function show($id)
    {
        $sale = Sale::with(['items.product', 'customer'])->findOrFail($id);
        return response()->json($sale);
    }

    public function index()
    {
        $sales = Sale::with(['items.product', 'customer'])->orderBy('created_at', 'desc')->get();
        return response()->json($sales);
    }
}
