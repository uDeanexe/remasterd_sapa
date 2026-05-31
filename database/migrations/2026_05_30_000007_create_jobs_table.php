<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            $table->string('client_name')->nullable();
            $table->string('whatsapp_number', 32)->nullable();
            $table->text('location')->nullable();
            $table->string('google_maps_link', 2048)->nullable();

            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable();
            $table->dateTime('accepted_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->integer('actual_duration')->nullable();
            $table->text('completion_reason')->nullable();

            $table->foreignId('cs_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->constrained('users')->cascadeOnDelete();

            $table->enum('status', ['pending', 'process', 'completed'])->default('pending');
            $table->text('feedback')->nullable();
            $table->integer('current_step')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};

