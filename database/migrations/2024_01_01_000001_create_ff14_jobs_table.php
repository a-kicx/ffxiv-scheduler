<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ff14_jobs', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('abbreviation', 3)->unique();
            $table->string('name', 50);
            $table->string('name_en', 50);
            $table->enum('role', ['tank', 'healer', 'melee', 'pranged', 'mranged']);
            $table->unsignedTinyInteger('sort_order')->default(0);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ff14_jobs');
    }
};
