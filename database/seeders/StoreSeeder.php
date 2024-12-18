<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Store;

class StoreSeeder extends Seeder
{
    public function run()
    {
        Store::create([
            'name' => 'Loja Matriz',
            'document' => '12.345.678/0001-90',
            'phone' => '(11) 99999-9999',
            'email' => 'matriz@example.com',
            'address' => 'Rua Principal, 123',
            'is_matrix' => true
        ]);
    }
}
