<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'cpf',
        'address',
        'city',
        'state',
        'postal_code',
        'active'
    ];

    protected $casts = [
        'active' => 'boolean',
    ];
}
