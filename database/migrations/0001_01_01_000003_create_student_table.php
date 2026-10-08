<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student', function (Blueprint $table) {
            $table->string('student_id', 20)->primary();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('email', 100)->unique();
            $table->string('password_hash');
            $table->boolean('data_privacy_agreed')->default(false);
            $table->unsignedInteger('section_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('section_id')
                  ->references('section_id')->on('section')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student');
    }
};