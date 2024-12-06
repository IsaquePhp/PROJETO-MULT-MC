<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('magalu_id')->nullable()->after('id');
            $table->string('magalu_sku')->nullable()->after('sku');
            $table->string('image_url')->nullable()->after('description');
            $table->decimal('weight', 10, 3)->nullable()->after('status');
            $table->decimal('height', 10, 2)->nullable()->after('weight');
            $table->decimal('width', 10, 2)->nullable()->after('height');
            $table->decimal('length', 10, 2)->nullable()->after('width');
            $table->timestamp('last_sync_at')->nullable()->after('length');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'magalu_id',
                'magalu_sku',
                'image_url',
                'weight',
                'height',
                'width',
                'length',
                'last_sync_at'
            ]);
        });
    }
};
