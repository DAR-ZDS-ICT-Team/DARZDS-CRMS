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
        Schema::table('divisions', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->after('id')->nullable();
            $table->foreign('office_id')
                  ->references('id')
                  ->on('offices')
                  ->onDelete('set null'); // or 'cascade' depending on your preference
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('divisions', function (Blueprint $table) {
            // Remove foreign key constraint first
            $table->dropForeign(['office_id']);
            
            // Then drop the column
            $table->dropColumn('office_id');
        });
    }
};
