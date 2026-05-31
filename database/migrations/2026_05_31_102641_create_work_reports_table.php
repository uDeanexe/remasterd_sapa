<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('work_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('report_date');
            $table->text('note')->nullable();
            $table->json('job_ids')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'report_date']);
            $table->index(['report_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_reports');
    }
};
