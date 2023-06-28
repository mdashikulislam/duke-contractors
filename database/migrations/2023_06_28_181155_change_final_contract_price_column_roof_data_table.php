<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roof_data', function (Blueprint $table) {
            $table->string('final_contract_price')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('roof_data', function (Blueprint $table) {
            //
        });
    }
};
