<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->tinyInteger('is_estimate')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            //
        });
    }
};
