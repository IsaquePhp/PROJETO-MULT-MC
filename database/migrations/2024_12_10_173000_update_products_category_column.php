<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove a coluna category antiga
            $table->dropColumn('category');
            
            // Adiciona a nova coluna category_id
            $table->foreignId('category_id')
                  ->nullable()
                  ->constrained()
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            // Remove a coluna category_id
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
            
            // Adiciona a coluna category de volta
            $table->string('category');
        });
    }
};
