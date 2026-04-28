<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->date('slot_date');
            $table->time('slot_time')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->unique(['event_id', 'slot_date', 'slot_time']);
            $table->index(['event_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slots');
    }
};
