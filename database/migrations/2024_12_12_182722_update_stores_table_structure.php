<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStoresTableStructure extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stores', function (Blueprint $table) {
            if (!Schema::hasColumn('stores', 'company_id')) {
                $table->foreignId('company_id')->after('id')->constrained()->onDelete('cascade');
            }
            if (!Schema::hasColumn('stores', 'name')) {
                $table->string('name');
            }
            if (!Schema::hasColumn('stores', 'document')) {
                $table->string('document')->nullable();
            }
            if (!Schema::hasColumn('stores', 'phone')) {
                $table->string('phone')->nullable();
            }
            if (!Schema::hasColumn('stores', 'email')) {
                $table->string('email')->nullable();
            }
            if (!Schema::hasColumn('stores', 'address')) {
                $table->string('address')->nullable();
            }
            if (!Schema::hasColumn('stores', 'is_matrix')) {
                $table->boolean('is_matrix')->default(false);
            }
            if (!Schema::hasColumn('stores', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropForeign(['company_id']);
            $table->dropColumn([
                'company_id',
                'name',
                'document',
                'phone',
                'email',
                'address',
                'is_matrix',
                'deleted_at'
            ]);
        });
    }
}
