<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('section', function (Blueprint $table) {
            $table->increments('section_id');
            $table->string('section_name', 50);
            $table->unsignedInteger('admin_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('admin_id')
                  ->references('admin_id')->on('admin')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('section');
    }
};