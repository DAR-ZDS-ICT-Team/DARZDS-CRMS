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
        Schema::create('sub_section_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sub_section_id');
            $table->foreign('sub_section_id')
                    ->references('id')
                    ->on('sub_sections');
            $table->foreignId('office_id');
            $table->foreign('office_id')
                    ->references('id')
                    ->on('offices');
            $table->string('type_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sub_unit_types');
    }
};
