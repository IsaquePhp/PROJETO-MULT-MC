<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInventoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inventories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->decimal('quantity', 10, 2);
            $table->decimal('min_quantity', 10, 2)->default(0);
            $table->decimal('max_quantity', 10, 2)->nullable();
            $table->string('location')->nullable(); // Physical location in the store
            $table->timestamps();
            $table->softDeletes();
            
            // Composite unique key
            $table->unique(['store_id', 'product_id']);
        });

        // Create inventory_movements table
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('store_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('type'); // in, out
            $table->decimal('quantity', 10, 2);
            $table->string('reason'); // sale, purchase, adjustment, transfer, etc
            $table->string('reference')->nullable(); // sale_id, purchase_id, etc
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inventory_movements');
        Schema::dropIfExists('inventories');
    }
}
