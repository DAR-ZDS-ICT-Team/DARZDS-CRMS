<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('id');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices')
                ->onDelete('restrict');
        });

        DB::statement('
            UPDATE services
            INNER JOIN divisions ON divisions.id = services.division_id
            SET services.office_id = divisions.office_id
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropForeign(['office_id']);
            $table->dropColumn('office_id');
        });
    }
};
