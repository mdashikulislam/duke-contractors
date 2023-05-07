<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('company_products', function (Blueprint $table) {
            $table->id();
            $table->integer('company_id')->default(0);
            $table->integer('product_id')->default(0);
            $table->integer('dim_covers')->nullable();
            $table->integer('unit_price')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_products');
    }
};
