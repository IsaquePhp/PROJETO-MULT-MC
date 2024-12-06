<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf_cnpj',
        'type',
        'status',
        'notes',
        'address',
        'address_number',
        'complement',
        'neighborhood',
        'city',
        'state',
        'zip_code',
        'company_name',
        'trading_name',
        'state_registration',
        'credit_limit',
        'total_debt',
        'available_credit',
        'company_id'
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'total_debt' => 'decimal:2',
        'available_credit' => 'decimal:2',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    // Calcula o crédito disponível
    public function updateAvailableCredit()
    {
        $this->available_credit = $this->credit_limit - $this->total_debt;
        $this->save();
    }

    // Verifica se o cliente tem crédito disponível para uma compra
    public function hasAvailableCredit($amount)
    {
        return $this->available_credit >= $amount;
    }
}
