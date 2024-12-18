<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Não faz nada no up
    }

    public function down()
    {
        // Remove todas as tabelas que podem estar duplicadas
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sale_items');
        Schema::dropIfExists('customers');
        Schema::dropIfExists('clients');
    }
};
