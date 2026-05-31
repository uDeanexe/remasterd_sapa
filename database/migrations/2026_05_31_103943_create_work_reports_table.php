<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('work_reports')) {
            Schema::create('work_reports', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('period_start')->index();
                $table->date('period_end')->index();
                $table->string('title', 255);
                $table->longText('summary');
                $table->timestamps();

                $table->index(['user_id', 'period_start', 'period_end']);
            });

            return;
        }

        // Legacy table exists: upgrade schema from (report_date, note, job_ids) to new period-based report.
        Schema::table('work_reports', function (Blueprint $table) {
            if (! Schema::hasColumn('work_reports', 'period_start')) {
                $table->date('period_start')->nullable()->index();
            }
            if (! Schema::hasColumn('work_reports', 'period_end')) {
                $table->date('period_end')->nullable()->index();
            }
            if (! Schema::hasColumn('work_reports', 'title')) {
                $table->string('title', 255)->nullable();
            }
            if (! Schema::hasColumn('work_reports', 'summary')) {
                $table->longText('summary')->nullable();
            }
        });

        // Backfill from legacy columns when present.
        if (Schema::hasColumn('work_reports', 'report_date')) {
            DB::table('work_reports')
                ->whereNull('period_start')
                ->update(['period_start' => DB::raw('report_date')]);
            DB::table('work_reports')
                ->whereNull('period_end')
                ->update(['period_end' => DB::raw('report_date')]);
        }
        if (Schema::hasColumn('work_reports', 'note')) {
            DB::table('work_reports')
                ->whereNull('summary')
                ->update(['summary' => DB::raw('note')]);
        }
        DB::table('work_reports')->whereNull('title')->update(['title' => 'Report Kerja']);

        // Make new columns required for new writes (keep existing rows safe).
        Schema::table('work_reports', function (Blueprint $table) {
            // For MySQL, changing nullability requires doctrine/dbal; avoid hard change here.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('work_reports');
    }
};
