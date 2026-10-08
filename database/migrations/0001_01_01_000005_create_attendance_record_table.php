<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_record', function (Blueprint $table) {
            $table->increments('record_id');
            $table->unsignedInteger('session_id');
            $table->string('student_id', 20);
            $table->dateTime('time_in');
            $table->enum('status', ['present', 'absent']);
            $table->timestamp('submitted_at')->useCurrent();

            $table->unique(['session_id', 'student_id'], 'unique_record');

            $table->foreign('session_id')
                  ->references('session_id')->on('attendance_session')
                  ->onDelete('cascade');

            $table->foreign('student_id')
                  ->references('student_id')->on('student')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_record');
    }
};