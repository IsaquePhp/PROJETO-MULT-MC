<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'document',
        'phone',
        'email',
        'address',
        'is_matrix'
    ];

    protected $casts = [
        'is_matrix' => 'boolean'
    ];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public static function getMatrix()
    {
        return static::where('is_matrix', true)->first();
    }
}
