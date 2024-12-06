<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('cpf_cnpj')->unique();
            $table->string('type')->default('individual'); // individual ou company
            $table->string('status')->default('active');
            $table->text('notes')->nullable();
            
            // Endereço
            $table->string('address')->nullable();
            $table->string('address_number')->nullable();
            $table->string('complement')->nullable();
            $table->string('neighborhood')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('zip_code')->nullable();
            
            // Campos para pessoa jurídica
            $table->string('company_name')->nullable();
            $table->string('trading_name')->nullable();
            $table->string('state_registration')->nullable();
            
            // Campos financeiros
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->decimal('total_debt', 10, 2)->default(0);
            $table->decimal('available_credit', 10, 2)->default(0);
            
            $table->foreignId('company_id')->constrained();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('customers');
    }
};
