<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('divisions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();

            $table->string('step_1')->default('Persiapan');
            $table->string('step_2')->default('Proses');
            $table->string('step_3')->default('Eksekusi');
            $table->string('step_4')->default('Selesai & Kendala');

            $table->timestamps();

            $table->boolean('req_photo_1')->default(false);
            $table->boolean('req_video_1')->default(false);
            $table->boolean('req_photo_2')->default(false);
            $table->boolean('req_video_2')->default(false);
            $table->boolean('req_photo_3')->default(false);
            $table->boolean('req_video_3')->default(false);
            $table->boolean('req_photo_4')->default(false);
            $table->boolean('req_video_4')->default(false);

            $table->boolean('req_desc_1')->default(false);
            $table->boolean('req_desc_2')->default(false);
            $table->boolean('req_desc_3')->default(false);
            $table->boolean('req_desc_4')->default(false);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('divisions');
    }
};
