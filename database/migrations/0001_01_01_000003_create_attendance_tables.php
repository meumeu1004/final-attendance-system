<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('admin_id');
            $table->string('name', 100);
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('section', function (Blueprint $table) {
            $table->increments('section_id');
            $table->string('section_name', 50);
            $table->unsignedInteger('admin_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('admin_id')
                ->references('admin_id')
                ->on('admin')
                ->cascadeOnDelete();
        });

        Schema::create('student', function (Blueprint $table) {
            $table->string('student_id', 20)->primary();
            $table->string('last_name', 50);
            $table->string('first_name', 50);
            $table->string('email', 100)->unique();
            $table->string('password_hash', 255);
            $table->boolean('data_privacy_agreed')->default(false);
            $table->unsignedInteger('section_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('section_id')
                ->references('section_id')
                ->on('section')
                ->restrictOnDelete()
                ->cascadeOnUpdate();
        });

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
                ->references('section_id')
                ->on('section')
                ->cascadeOnDelete();
            $table->foreign('admin_id')
                ->references('admin_id')
                ->on('admin')
                ->cascadeOnDelete();
        });

        Schema::create('attendance_record', function (Blueprint $table) {
            $table->increments('record_id');
            $table->unsignedInteger('session_id');
            $table->string('student_id', 20);
            $table->dateTime('time_in')->nullable();
            $table->enum('status', ['present', 'absent']);
            $table->timestamp('submitted_at')->useCurrent();

            $table->unique(['session_id', 'student_id'], 'unique_record');
            $table->foreign('session_id')
                ->references('session_id')
                ->on('attendance_session')
                ->cascadeOnDelete();
            $table->foreign('student_id')
                ->references('student_id')
                ->on('student')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_record');
        Schema::dropIfExists('attendance_session');
        Schema::dropIfExists('student');
        Schema::dropIfExists('section');
        Schema::dropIfExists('admin');
    }
};
