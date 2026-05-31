<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('a_user_role', function (Blueprint $table) {
            $table->string('role_id', 15)->primary();
            $table->string('role_name', 25);
            $table->string('description', 100)->nullable();
            $table->date('transaction_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('a_user_role');
    }
};
