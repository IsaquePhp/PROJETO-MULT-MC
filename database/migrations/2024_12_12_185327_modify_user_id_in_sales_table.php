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
        Schema::table('sales', function (Blueprint $table) {
            // Drop the foreign key constraint and column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            // Recreate the column as nullable
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // Drop the nullable column
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });

        Schema::table('sales', function (Blueprint $table) {
            // Recreate the original non-nullable column
            $table->unsignedBigInteger('user_id')->after('id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }
};
