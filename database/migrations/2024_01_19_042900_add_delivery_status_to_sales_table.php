<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('total');
            $table->timestamp('processing_start')->nullable()->after('status');
            $table->timestamp('ready_at')->nullable()->after('processing_start');
            $table->timestamp('delivery_start')->nullable()->after('ready_at');
            $table->timestamp('delivered_at')->nullable()->after('delivery_start');
            $table->string('delivery_address')->nullable()->after('delivered_at');
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'status',
                'processing_start',
                'ready_at',
                'delivery_start',
                'delivered_at',
                'delivery_address'
            ]);
        });
    }
};
