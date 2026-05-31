<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('a_user_system', function (Blueprint $table) {
            $table->id();
            $table->string('hdrid', 50)->nullable();
            $table->string('role_id', 15)->nullable();
            $table->string('role_name', 50)->nullable();
            $table->string('nik', 50)->nullable();
            $table->string('username', 50)->nullable();
            $table->date('transaction_date')->nullable();
            $table->string('kode_department', 50)->nullable();
            $table->string('nama_department', 50)->nullable();
            $table->timestamps();

            $table->index(['nik', 'username']);
            $table->index(['role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('a_user_system');
    }
};
