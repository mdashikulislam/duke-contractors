<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roof_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->foreign('lead_id')->references('id')->on('leads')->onDelete('cascade');
            $table->string('combination')->nullable();
            $table->text('roof_snap')->nullable();
            $table->text('eagle_view')->nullable();
            $table->decimal('miscellaneous',10,2)->default('0');
            $table->decimal('desire_profit',10,2)->default('0');
            $table->decimal('seller_commission',10,2)->default('0');
            $table->decimal('office_commission',10,2)->default('0');
            $table->decimal('final_contract_price',10,2)->default('0');
            $table->decimal('labor_total',10,2)->default('0');
            $table->decimal('trash_total',10,2)->default('0');
            $table->decimal('permit_total',10,2)->default('0');
            $table->decimal('supplies_total',10,2)->default('0');
            $table->timestamps();
        });
        Schema::table('roof_types',function (Blueprint $table){
            $table->dropColumn('roof_snap');
            $table->dropColumn('eagle_view');
            $table->dropColumn('miscellaneous');
            $table->dropColumn('desire_profit');
            $table->dropColumn('seller_commission');
            $table->dropColumn('office_commission');
            $table->dropColumn('final_contract_price');
            $table->dropColumn('labor_total');
            $table->dropColumn('trash_total');
            $table->dropColumn('permit_total');
            $table->dropColumn('supplies_total');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roof_data');
    }
};
