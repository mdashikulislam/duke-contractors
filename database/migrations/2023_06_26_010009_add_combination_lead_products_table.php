<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('lead_products', function (Blueprint $table) {
            $table->string('combination')->index()->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('lead_products', function (Blueprint $table) {
            //
        });
    }
};
