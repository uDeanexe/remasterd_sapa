<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('a_user_role_access', function (Blueprint $table) {
            $table->string('hdrid', 50)->primary();
            $table->string('role_id', 15)->nullable();
            $table->string('role_name', 50)->nullable();
            $table->string('id_menu', 50)->nullable();
            $table->string('menu_name', 50)->nullable();
            $table->boolean('allow_add')->default(false);
            $table->boolean('allow_edit')->default(false);
            $table->boolean('allow_delete')->default(false);
            $table->boolean('allow_view')->default(true);
            $table->boolean('allow_export')->default(false);
            $table->date('transaction_date')->nullable();
            $table->timestamps();

            $table->index(['role_id', 'id_menu']);
            $table->foreign('role_id')->references('role_id')->on('a_user_role')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('a_user_role_access');
    }
};
