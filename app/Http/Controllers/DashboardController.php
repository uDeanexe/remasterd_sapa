<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\RecruitmentCandidate;
use App\Models\RecruitmentOpening;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        $role = strtolower((string) ($user->role ?? ''));

        if (in_array($role, ['teknisi', 'karyawan'], true)) {
            $route = $role === 'teknisi' ? 'teknisi.dashboard' : 'karyawan.dashboard';
            return redirect()->route($route);
        }

        $payload = match ($role) {
            'cs' => $this->buildCsPayload(),
            'hr' => $this->buildHrPayload(),
            'finance' => $this->buildFinancePayload(),
            default => $this->buildAdminPayload(),
        };

        return view('dashboard', $payload + ['dashboardRole' => $role]);
    }

    private function buildAdminPayload(): array
    {
        $today = now()->toDateString();
        $yesterday = now()->subDay()->toDateString();

        $todayJobs = Job::with(['cs', 'technician.division'])
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        $yesterdayJobs = Job::with(['cs', 'technician.division'])
            ->whereDate('created_at', $yesterday)
            ->latest()
            ->get();

        $activeJobs = Job::with(['cs', 'technician.division'])
            ->whereIn('status', ['pending', 'process'])
            ->latest()
            ->limit(8)
            ->get();

        $allJobs = Job::all();
        $todayPresences = Presence::with('user')
            ->whereDate('date', $today)
            ->get();

        $pendingPresenceApprovals = Presence::where(function ($query) {
                $query->where('is_approved', 'pending')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('check_out')
                            ->where(function ($approvalQuery) {
                                $approvalQuery->where('is_approved_out', 'pending')
                                    ->orWhereNull('is_approved_out');
                            });
                    });
            })
            ->count();

        $pendingLeaves = Leave::where('status', 'pending')->count();
        $employeesCount = User::whereIn('role', ['karyawan', 'teknisi'])->count();

        $summary = [
            'today_jobs' => $todayJobs->count(),
            'yesterday_jobs' => $yesterdayJobs->count(),
            'active_jobs' => $activeJobs->count(),
            'completed_jobs' => $allJobs->where('status', 'completed')->count(),
            'pending_jobs' => $allJobs->where('status', 'pending')->count(),
            'process_jobs' => $allJobs->where('status', 'process')->count(),
            'overdue_jobs' => $allJobs->filter->is_overdue->count(),
            'today_attendance' => $todayPresences->count(),
            'pending_presence_approvals' => $pendingPresenceApprovals,
            'pending_leaves' => $pendingLeaves,
            'employees' => $employeesCount,
        ];

        return compact('summary', 'todayJobs', 'yesterdayJobs', 'activeJobs', 'todayPresences');
    }

    private function buildCsPayload(): array
    {
        $userId = (int) Auth::id();
        $today = now()->toDateString();

        $todayJobs = Job::with(['technician.division'])
            ->where('cs_id', $userId)
            ->whereDate('created_at', $today)
            ->latest()
            ->get();

        $activeJobs = Job::with(['technician.division'])
            ->where('cs_id', $userId)
            ->whereIn('status', ['pending', 'process'])
            ->latest()
            ->limit(8)
            ->get();

        $recentJobs = Job::with(['technician.division'])
            ->where('cs_id', $userId)
            ->latest()
            ->limit(15)
            ->get();

        $summary = [
            'today' => $todayJobs->count(),
            'active' => $activeJobs->count(),
            'pending' => $recentJobs->where('status', 'pending')->count(),
            'process' => $recentJobs->where('status', 'process')->count(),
            'completed' => $recentJobs->where('status', 'completed')->count(),
        ];

        return compact('summary', 'activeJobs', 'recentJobs');
    }

    private function buildHrPayload(): array
    {
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $pendingPresenceApprovals = Presence::where('is_approved', 'pending')->count();

        $openings = RecruitmentOpening::query()->latest()->limit(10)->get();
        $candidates = RecruitmentCandidate::query()->latest()->limit(10)->get();

        $summary = [
            'pending_leaves' => $pendingLeaves,
            'pending_presence' => $pendingPresenceApprovals,
            'openings' => RecruitmentOpening::count(),
            'candidates' => RecruitmentCandidate::count(),
        ];

        return compact('summary', 'openings', 'candidates');
    }

    private function buildFinancePayload(): array
    {
        $jobs = Job::query()->latest()->limit(30)->get();
        $summary = [
            'total' => Job::count(),
            'pending' => Job::where('status', 'pending')->count(),
            'process' => Job::where('status', 'process')->count(),
            'completed' => Job::where('status', 'completed')->count(),
            'overdue' => $jobs->filter->is_overdue->count(),
        ];

        return compact('summary', 'jobs');
    }
}
