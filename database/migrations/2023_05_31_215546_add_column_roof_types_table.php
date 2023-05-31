<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roof_types', function (Blueprint $table) {
            $table->decimal('miscellaneous',10,2)->default(0);
            $table->decimal('desire_profit',10,2)->default(0);
            $table->decimal('seller_commission',10,2)->default(0);
            $table->decimal('office_commission',10,2)->default(0);
            $table->string('final_contract_price',)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('roof_types', function (Blueprint $table) {
            //
        });
    }
};
