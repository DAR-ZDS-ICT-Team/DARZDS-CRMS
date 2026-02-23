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
        Schema::table('c_s_f_forms', function (Blueprint $table) {

            $table->dropForeign(['sub_section_id']);
            
            $table->dropColumn(['sub_section_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_s_f_forms', function (Blueprint $table) {
          
            $table->unsignedBigInteger('sub_section_id');
            
        
            $table->foreign('sub_section_id')
                  ->references('id')
                  ->on('sub_sections')
                  ->onDelete('set null'); 
        });
    }
};
