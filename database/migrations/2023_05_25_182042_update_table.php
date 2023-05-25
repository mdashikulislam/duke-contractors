<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roof_types', function (Blueprint $table) {
            $table->integer('company_id')->default(0);
        });
        Schema::table('lead_products', function (Blueprint $table) {
            $table->dropForeign('lead_products_company_id_foreign');
        });
        Schema::table('lead_products', function (Blueprint $table) {
            $table->dropColumn('company_id');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
};
