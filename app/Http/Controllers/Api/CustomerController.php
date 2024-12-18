<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();

        // Filtro por busca
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%")
                  ->orWhere('cpf_cnpj', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            });
        }

        // Filtro por tipo
        if ($request->type) {
            $query->where('type', $request->type);
        }

        // Filtro por status
        if ($request->status) {
            $query->where('status', $request->status);
        }

        // Filtro por cidade
        if ($request->city) {
            $query->where('city', $request->city);
        }

        // Ordenação
        $query->orderBy($request->sort_by ?? 'created_at', $request->sort_order ?? 'desc');

        $customers = $query->paginate($request->per_page ?? 10);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:customers',
            'phone' => 'nullable|string|max:20',
            'cpf_cnpj' => 'required|string|unique:customers',
            'type' => 'required|in:individual,company',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'company_name' => 'nullable|required_if:type,company|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'state_registration' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0',
            'company_id' => 'required|exists:companies,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $customer = Customer::create($request->all());
            $customer->updateAvailableCredit();

            return response()->json([
                'message' => 'Cliente criado com sucesso',
                'customer' => $customer
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao criar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function show(Customer $customer)
    {
        $customer->load(['payments', 'sales']);
        return response()->json($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['nullable', 'email', Rule::unique('customers')->ignore($customer->id)],
            'phone' => 'nullable|string|max:20',
            'cpf_cnpj' => ['required', 'string', Rule::unique('customers')->ignore($customer->id)],
            'type' => 'required|in:individual,company',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'address' => 'nullable|string|max:255',
            'address_number' => 'nullable|string|max:20',
            'complement' => 'nullable|string|max:100',
            'neighborhood' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:2',
            'zip_code' => 'nullable|string|max:10',
            'company_name' => 'nullable|required_if:type,company|string|max:255',
            'trading_name' => 'nullable|string|max:255',
            'state_registration' => 'nullable|string|max:50',
            'credit_limit' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $customer->update($request->all());
            $customer->updateAvailableCredit();

            return response()->json([
                'message' => 'Cliente atualizado com sucesso',
                'customer' => $customer
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao atualizar cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            $customer->delete();
            return response()->json(['message' => 'Cliente excluído com sucesso']);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Erro ao excluir cliente',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Retorna o histórico de compras do cliente
    public function salesHistory(Customer $customer)
    {
        $sales = $customer->sales()
            ->with(['items.product', 'payments'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($sales);
    }

    // Retorna o histórico de pagamentos do cliente
    public function paymentHistory(Customer $customer)
    {
        $payments = $customer->payments()
            ->with(['installments', 'sale'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($payments);
    }

    public function search(Request $request)
    {
        $search = $request->search;
        
        if (empty($search) || strlen($search) < 2) {
            return response()->json(['data' => []]);
        }

        $customers = Customer::where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('cpf_cnpj', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json(['data' => $customers]);
    }
}
