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
        Schema::create('assignatorees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id');
            $table->foreign('office_id')
                ->references('id')
                ->on('offices');
            $table->string('name');
            $table->string('designation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignatorees');
    }
};
