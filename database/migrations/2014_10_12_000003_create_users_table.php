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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('account_type')->default('user');
            $table->string('designation')->nullable();
            $table->foreignId('office_id')->nullable();
            $table->foreign('office_id')
                    ->references('id')
                    ->on('offices');
            $table->foreignId('division_id')->nullable();
            $table->foreign('division_id')
                ->references('id')
                ->on('divisions');
            $table->foreignId('section_id')->nullable();
            $table->foreign('section_id')
                ->references('id')
                ->on('sections');
            $table->string('profile_photo_path', 2048)->nullable();
            $table->boolean('is_active')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
