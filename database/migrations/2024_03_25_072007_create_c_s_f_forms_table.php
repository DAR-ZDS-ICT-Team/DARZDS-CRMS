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
        Schema::create('c_s_f_forms', function (Blueprint $table) {
            $table->id();
            $table->string('control_number')->unique(); 
            $table->foreignId('customer_id');
            $table->foreign('customer_id')
                  ->references('id')
                  ->on('customers');
            $table->foreignId('office_id');
            $table->foreign('office_id')
                    ->references('id')
                    ->on('offices');
            $table->foreignId('division_id');
            $table->foreign('division_id')
                    ->references('id')
                    ->on('divisions');
            $table->foreignId('section_id')->nullable();;
            $table->foreign('section_id')
                    ->references('id')
                    ->on('sections');
            $table->foreignId('sub_section_id')->nullable();
            $table->foreign('sub_section_id')
                    ->references('id')
                    ->on('sub_sections');
            $table->string('client_type')->nullable();
            $table->string('sub_section_type')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c_s_f_forms');
    }
};
