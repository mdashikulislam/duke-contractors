<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('company_product_id')->nullable();
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->foreign('company_product_id')->references('id')->on('company_products')->onDelete('cascade');
            $table->integer('quantity')->default(0);
            $table->string('type',20)->nullable();
            $table->string('category',20)->nullable();
            $table->decimal('cost',10,2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_products');
    }
};
