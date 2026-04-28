<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->cascadeOnDelete();
            $table->char('participant_token', 64);
            $table->string('name', 100);
            $table->json('selected_jobs')->nullable();
            $table->json('selected_sub_jobs')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['event_id', 'participant_token']);
            $table->index('participant_token');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
