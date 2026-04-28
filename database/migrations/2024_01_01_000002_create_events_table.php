<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->char('ulid', 26)->unique();
            $table->char('admin_token', 64);
            $table->string('name');
            $table->enum('party_type', ['light', 'full', 'alliance', 'alliance_special']);
            $table->json('role_config');
            $table->enum('job_mode', ['none', 'single', 'multiple'])->default('none');
            $table->enum('sub_job_mode', ['none', 'single', 'multiple'])->default('none');
            $table->enum('attendance_mode', ['binary', 'ternary'])->default('ternary');
            $table->timestamps();

            $table->index('admin_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
