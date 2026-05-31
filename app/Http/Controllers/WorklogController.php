<?php

namespace App\Http\Controllers;

use App\Models\WorkNote;
use App\Models\WorkReport;
use App\Models\WorkTimeline;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class WorklogController extends Controller
{
    private function normalizedRole(): string
    {
        $role = strtolower((string) (Auth::user()->role ?? ''));

        return match ($role) {
            'admin', 'kepala' => 'administrator',
            'karyawan', 'technician' => 'teknisi',
            default => $role,
        };
    }

    private function canViewAll(): bool
    {
        return in_array($this->normalizedRole(), ['administrator', 'hr'], true);
    }

    private function ensureAllowed(): void
    {
        // Finance: null dulu (tidak ikut modul worklog).
        abort_if($this->normalizedRole() === 'finance', 403);
    }

    public function index(Request $request)
    {
        $this->ensureAllowed();

        $tab = (string) $request->query('tab', 'timeline');
        $tab = in_array($tab, ['timeline', 'notes', 'reports'], true) ? $tab : 'timeline';

        $month = (int) $request->query('month', (int) now()->month);
        $year = (int) $request->query('year', (int) now()->year);
        $month = max(1, min(12, $month));
        $year = max(2000, min(2100, $year));
        $q = trim((string) $request->query('q', ''));

        $viewAll = $this->canViewAll();
        $userId = (int) Auth::id();

        $monthStart = now()->setYear($year)->setMonth($month)->startOfMonth()->startOfDay();
        $monthEnd = $monthStart->copy()->endOfMonth()->endOfDay();

        $timelineItems = WorkTimeline::query()
            ->with('user:id,name')
            ->whereBetween('work_date', [$monthStart->toDateString(), $monthEnd->toDateString()])
            ->when(! $viewAll, fn ($query) => $query->where('user_id', $userId))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($w) use ($q) {
                    $w->where('title', 'like', '%'.$q.'%')
                        ->orWhere('description', 'like', '%'.$q.'%')
                        ->orWhereHas('user', fn ($u) => $u->where('name', 'like', '%'.$q.'%'));
                });
            })
            ->orderBy('work_date')
            ->orderBy('id')
            ->get();

        $notes = WorkNote::query()
            ->with('user:id,name')
            ->when(! $viewAll, fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('note_date')
            ->orderByDesc('id')
            ->limit(200)
            ->get();

        $reports = WorkReport::query()
            ->with('user:id,name')
            ->when(! $viewAll, fn ($q) => $q->where('user_id', $userId))
            ->orderByDesc('period_end')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('worklog.index', compact(
            'tab',
            'timelineItems',
            'notes',
            'reports',
            'viewAll',
            'month',
            'year',
            'q',
            'monthStart',
        ));
    }

    public function storeTimeline(Request $request)
    {
        $this->ensureAllowed();

        $validated = $request->validate([
            'work_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'status' => ['required', Rule::in(['planned', 'in_progress', 'done', 'blocked'])],
        ]);

        WorkTimeline::query()->create([
            'user_id' => Auth::id(),
            'work_date' => $validated['work_date'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'status' => $validated['status'],
        ]);

        return redirect()->route('worklog.index', ['tab' => 'timeline'])->with('success', 'Timeline kerja tersimpan.');
    }

    public function destroyTimeline(WorkTimeline $timeline)
    {
        $this->ensureAllowed();

        abort_unless($this->canViewAll() || (int) $timeline->user_id === (int) Auth::id(), 403);

        $timeline->delete();

        return redirect()->route('worklog.index', ['tab' => 'timeline'])->with('success', 'Timeline kerja dihapus.');
    }

    public function storeNote(Request $request)
    {
        $this->ensureAllowed();

        $validated = $request->validate([
            'note_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:20000'],
            'tags' => ['nullable', 'string', 'max:255'],
        ]);

        WorkNote::query()->create([
            'user_id' => Auth::id(),
            'note_date' => $validated['note_date'],
            'title' => $validated['title'],
            'body' => $validated['body'],
            'tags' => $validated['tags'] ?? null,
        ]);

        return redirect()->route('worklog.index', ['tab' => 'notes'])->with('success', 'Note kerja tersimpan.');
    }

    public function destroyNote(WorkNote $note)
    {
        $this->ensureAllowed();

        abort_unless($this->canViewAll() || (int) $note->user_id === (int) Auth::id(), 403);

        $note->delete();

        return redirect()->route('worklog.index', ['tab' => 'notes'])->with('success', 'Note kerja dihapus.');
    }

    public function storeReport(Request $request)
    {
        $this->ensureAllowed();

        $validated = $request->validate([
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'title' => ['required', 'string', 'max:255'],
            'summary' => ['required', 'string', 'max:50000'],
        ]);

        WorkReport::query()->create([
            'user_id' => Auth::id(),
            'period_start' => $validated['period_start'],
            'period_end' => $validated['period_end'],
            'title' => $validated['title'],
            'summary' => $validated['summary'],
        ]);

        return redirect()->route('worklog.index', ['tab' => 'reports'])->with('success', 'Report kerja tersimpan.');
    }

    public function destroyReport(WorkReport $report)
    {
        $this->ensureAllowed();

        abort_unless($this->canViewAll() || (int) $report->user_id === (int) Auth::id(), 403);

        $report->delete();

        return redirect()->route('worklog.index', ['tab' => 'reports'])->with('success', 'Report kerja dihapus.');
    }
}
