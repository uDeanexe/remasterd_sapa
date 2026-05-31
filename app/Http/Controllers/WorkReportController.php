<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Models\WorkReport;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorkReportController extends Controller
{
    public function technicianIndex(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 401);

        $today = now()->toDateString();
        $date = (string) $request->query('date', $today);
        $reportDate = Carbon::parse($date)->toDateString();

        $report = WorkReport::query()
            ->where('user_id', $user->id)
            ->where('report_date', $reportDate)
            ->first();

        $jobs = Job::query()
            ->where('technician_id', $user->id)
            ->where(function ($q) use ($reportDate) {
                $q->whereDate('accepted_at', $reportDate)
                    ->orWhereDate('completed_at', $reportDate)
                    ->orWhereDate('created_at', $reportDate);
            })
            ->latest('id')
            ->get();

        $recentReports = WorkReport::query()
            ->where('user_id', $user->id)
            ->orderByDesc('report_date')
            ->limit(14)
            ->get();

        return view('technician.work-reports.index', compact('report', 'jobs', 'reportDate', 'recentReports'));
    }

    public function technicianStore(Request $request)
    {
        $user = $request->user();
        abort_unless($user, 401);

        $validated = $request->validate([
            'report_date' => ['required', 'date'],
            'note' => ['nullable', 'string', 'max:5000'],
            'job_ids' => ['nullable', 'array'],
            'job_ids.*' => ['integer', Rule::exists('jobs', 'id')->where('technician_id', $user->id)],
        ]);

        $reportDate = Carbon::parse($validated['report_date'])->toDateString();

        WorkReport::query()->updateOrCreate(
            ['user_id' => $user->id, 'report_date' => $reportDate],
            [
                'note' => $validated['note'] ?? null,
                'job_ids' => array_values(array_unique(array_map('intval', $validated['job_ids'] ?? []))),
            ]
        );

        return redirect()
            ->route('teknisi.work-reports.index', ['date' => $reportDate])
            ->with('success', 'Laporan kerja berhasil disimpan.');
    }

    public function hrIndex(Request $request)
    {
        $month = (int) $request->query('month', now()->month);
        $year = (int) $request->query('year', now()->year);
        $month = max(1, min(12, $month));
        $year = max(2000, min(2100, $year));

        $userId = $request->query('user_id');
        $userId = is_numeric($userId) ? (int) $userId : null;

        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $technicians = User::query()
            ->whereIn('role', ['teknisi', 'karyawan'])
            ->orderBy('name')
            ->get();

        $reportsQuery = WorkReport::query()
            ->with('user:id,name,role')
            ->whereBetween('report_date', [$start->toDateString(), $end->toDateString()])
            ->orderByDesc('report_date');

        if ($userId) {
            $reportsQuery->where('user_id', $userId);
        }

        $reports = $reportsQuery->get();

        $jobsQuery = Job::query()
            ->with(['technician:id,name'])
            ->whereBetween('completed_at', [$start, $end]);

        if ($userId) {
            $jobsQuery->where('technician_id', $userId);
        }

        $completedJobs = $jobsQuery->get();

        $summary = [
            'completed_jobs' => $completedJobs->count(),
            'overdue_jobs' => $completedJobs->filter->is_overdue->count(),
            'avg_duration_minutes' => (int) round($completedJobs->whereNotNull('actual_duration')->avg('actual_duration') ?? 0),
        ];

        $months = ['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];

        return view('hr.work-reports.index', compact(
            'reports',
            'completedJobs',
            'summary',
            'technicians',
            'month',
            'year',
            'months',
            'userId'
        ));
    }
}
