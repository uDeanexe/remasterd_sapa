<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('users')
            ->whereIn('role', ['admin', 'kepala'])
            ->update(['role' => 'administrator']);

        DB::table('users')
            ->where('role', 'technician')
            ->update(['role' => 'teknisi']);

        DB::table('users')
            ->where('role', 'customer_service')
            ->update(['role' => 'cs']);
    }

    public function down(): void
    {
        // No-op: data normalization is not reversible safely.
    }
};
