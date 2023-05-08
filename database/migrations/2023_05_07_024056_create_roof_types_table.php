<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('roof_types', function (Blueprint $table) {
            $table->id();
            $table->integer('lead_id')->default(0);
            $table->tinyInteger('tile')->default(0);
            $table->tinyInteger('metal')->default(0);
            $table->tinyInteger('shingle')->default(0);
            $table->tinyInteger('flat')->default(0);
            $table->tinyInteger('tile_current')->default(0);
            $table->tinyInteger('metal_current')->default(0);
            $table->tinyInteger('shingle_current')->default(0);
            $table->tinyInteger('flat_current')->default(0);
            $table->longText('roof_snap')->nullable();
            $table->longText('eagle_view')->nullable();
            $table->decimal('tax',10,2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roof_types');
    }
};
