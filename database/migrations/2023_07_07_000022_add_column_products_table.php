<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('low_slope')->nullable();
            $table->text('steep_slope')->nullable();
        });
        Schema::table('product_categories',function (Blueprint $table){
            $table->dropColumn('low_slope');
            $table->dropColumn('steep_slope');
        });
    }

    public function down(): void
    {
        Schema::table('', function (Blueprint $table) {
            //
        });
    }
};
