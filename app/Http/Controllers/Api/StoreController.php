<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $query = Store::query();
        
        if ($request->has('company_id')) {
            $query->where('company_id', $request->company_id);
        }

        $stores = $query->with(['company'])->paginate(10);
        return response()->json($stores);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required|exists:companies,id',
            'name' => 'required|string|max:255',
            'cnpj' => 'required|string|unique:stores',
            'email' => 'required|email|unique:stores',
            'phone' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'state' => 'required|string',
            'zip_code' => 'required|string',
            'is_matrix' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $store = Store::create($request->all());
        return response()->json($store, 201);
    }

    public function show(Store $store)
    {
        $store->load(['company', 'inventory', 'sales', 'cashFlow']);
        return response()->json($store);
    }

    public function update(Request $request, Store $store)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'exists:companies,id',
            'name' => 'string|max:255',
            'cnpj' => 'string|unique:stores,cnpj,' . $store->id,
            'email' => 'email|unique:stores,email,' . $store->id,
            'phone' => 'string',
            'address' => 'string',
            'city' => 'string',
            'state' => 'string',
            'zip_code' => 'string',
            'is_matrix' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $store->update($request->all());
        return response()->json($store);
    }

    public function destroy(Store $store)
    {
        $store->delete();
        return response()->json(null, 204);
    }

    public function inventory(Store $store)
    {
        $inventory = $store->inventory()->with('product')->paginate(10);
        return response()->json($inventory);
    }

    public function sales(Store $store, Request $request)
    {
        $query = $store->sales()->with(['items.product', 'user']);

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $sales = $query->paginate(10);
        return response()->json($sales);
    }

    public function cashFlow(Store $store, Request $request)
    {
        $query = $store->cashFlow();

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $cashFlow = $query->paginate(10);
        return response()->json($cashFlow);
    }

    public function balance(Store $store, Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth());
        $endDate = $request->get('end_date', now()->endOfMonth());

        $balance = $store->cashFlow()
            ->selectRaw('
                SUM(CASE WHEN type = "income" THEN amount ELSE 0 END) as total_income,
                SUM(CASE WHEN type = "expense" THEN amount ELSE 0 END) as total_expense,
                SUM(CASE WHEN type = "income" THEN amount ELSE -amount END) as balance
            ')
            ->whereBetween('date', [$startDate, $endDate])
            ->first();

        return response()->json($balance);
    }
}
