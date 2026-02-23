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
            $table->unsignedBigInteger('service_id')->after('section_id')->nullable();
            $table->foreign('service_id')
                  ->references('id')
                  ->on('services')
                  ->onDelete('set null'); // or 'cascade' depending on your preference
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_s_f_forms', function (Blueprint $table) {
            // Remove foreign key constraint first
            $table->dropForeign(['service_id']);
            
            // Then drop the column
            $table->dropColumn('service_id');
        });
    }
};
