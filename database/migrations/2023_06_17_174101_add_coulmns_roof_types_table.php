<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('roof_types', function (Blueprint $table) {
            $table->decimal('labor_total',10,2)->default(0);
            $table->decimal('trash_total',10,2)->default(0);
            $table->decimal('permit_total',10,2)->default(0);
            $table->decimal('supplies_total',10,2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('roof_types', function (Blueprint $table) {
            //
        });
    }
};
