<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('type',20)->nullable();
            $table->string('product_categoty')->nullable();
            $table->tinyInteger('is_default')->default(0);
            $table->enum('wood_type',['None','Plywood','Fasica'])->default('None');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
