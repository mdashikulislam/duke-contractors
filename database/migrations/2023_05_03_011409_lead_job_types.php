<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('lead_job_types', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('lead_id')->default(0);
            $table->bigInteger('job_type_id')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::table('lead_job_types', function (Blueprint $table) {
            //
        });
    }
};
