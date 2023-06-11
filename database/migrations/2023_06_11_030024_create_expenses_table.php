<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->string('type',30)->index();
            $table->decimal('amount',30)->default(0);
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('other_companies')->onDelete('cascade');
            $table->text('description')->nullable();
            $table->enum('status',['Paid','Pending'])->nullable();
            $table->date('date')->nullable();
            $table->string('invoice',30)->nullable();
            $table->string('precio_por_sq',30)->nullable();
            $table->string('deck',30)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
