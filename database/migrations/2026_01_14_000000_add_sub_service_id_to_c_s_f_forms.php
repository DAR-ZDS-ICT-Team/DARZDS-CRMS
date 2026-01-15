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
            $table->unsignedBigInteger('sub_service_id')->nullable()->after('service_id');
            $table->foreign('sub_service_id')
                ->references('id')
                ->on('sub_services')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('c_s_f_forms', function (Blueprint $table) {
            $table->dropForeign(['sub_service_id']);
            $table->dropColumn('sub_service_id');
        });
    }
};
