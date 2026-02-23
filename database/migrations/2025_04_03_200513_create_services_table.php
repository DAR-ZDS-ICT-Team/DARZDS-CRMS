<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->foreignId('division_id')
                  ->constrained('divisions')
                  ->onDelete('restrict');
        
            $table->foreignId('section_id')->nullable(); // Allow NULL since some divisions have services directly
            $table->foreign('section_id')
                  ->references('id')
                  ->on('sections')
                  ->onDelete('restrict');
        
            $table->string('service_name');
            $table->string('service_url')->nullable();
            $table->boolean('is_disabled')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
