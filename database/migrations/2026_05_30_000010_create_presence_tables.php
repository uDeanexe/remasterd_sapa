<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('category')->default('masuk');
            $table->date('date');
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->string('photo_in')->nullable();
            $table->string('photo_out')->nullable();
            $table->double('lat_in')->nullable();
            $table->double('lng_in')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable();
            $table->enum('is_approved', ['pending', 'approved', 'rejected'])->default('pending');
            $table->enum('is_approved_out', ['pending', 'approved', 'rejected'])->nullable()->default('pending');
            $table->string('lat_out')->nullable();
            $table->string('lng_out')->nullable();
            $table->text('notes_out')->nullable();
            $table->timestamps();
        });

        Schema::create('leaves', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['izin', 'sakit', 'cuti'])->default('izin');
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('reason')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->string('attachment')->nullable();
            $table->timestamps();
        });

        Schema::create('office_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('radius')->default(50);
            $table->boolean('radius_enforced')->default(true);
            $table->string('check_in_time')->default('08:00');
            $table->string('check_out_time')->default('17:00');
            $table->unsignedInteger('late_tolerance')->default(15);
            $table->timestamps();
        });

        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->date('holiday_date')->unique();
            $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('holidays');
        Schema::dropIfExists('office_settings');
        Schema::dropIfExists('leaves');
        Schema::dropIfExists('presences');
    }
};
