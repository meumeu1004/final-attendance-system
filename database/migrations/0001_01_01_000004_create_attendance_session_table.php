<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_session', function (Blueprint $table) {
            $table->increments('session_id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('admin_id');
            $table->dateTime('started_at');
            $table->dateTime('ended_at');
            $table->dateTime('deadline');
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->date('date');

            $table->foreign('section_id')
                  ->references('section_id')->on('section')
                  ->onDelete('cascade');

            $table->foreign('admin_id')
                  ->references('admin_id')->on('admin')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_session');
    }
};