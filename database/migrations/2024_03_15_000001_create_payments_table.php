<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained();
            $table->foreignId('sale_id')->nullable()->constrained();
            $table->foreignId('company_id')->constrained();
            $table->foreignId('store_id')->constrained();
            
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            
            $table->string('status'); // pending, paid, partial, late, cancelled
            $table->string('payment_method'); // money, credit_card, debit_card, pix, bank_slip
            $table->integer('installments')->default(1);
            
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            
            $table->string('bank_slip_code')->nullable();
            $table->string('pix_code')->nullable();
            $table->string('card_transaction_id')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        
        // Tabela para parcelas
        Schema::create('payment_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained();
            $table->integer('installment_number');
            $table->decimal('amount', 10, 2);
            $table->date('due_date');
            $table->date('payment_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
        
        // Tabela para notificações
        Schema::create('payment_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained();
            $table->string('type'); // due_date, late_payment, payment_received
            $table->string('status')->default('pending'); // pending, sent, failed
            $table->timestamp('scheduled_for');
            $table->timestamp('sent_at')->nullable();
            $table->text('message');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('payment_notifications');
        Schema::dropIfExists('payment_installments');
        Schema::dropIfExists('payments');
    }
};
