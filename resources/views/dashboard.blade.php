<x-app-layout>
    @php
        $dashboardRole = strtolower((string) ($dashboardRole ?? auth()->user()?->role ?? ''));
    @endphp

    @if($dashboardRole === 'cs')
        <div class="admin-shell">
            <div class="admin-container">
                <div class="admin-page-header">
                    <div class="admin-page-header-accent"></div>
                    <div class="admin-page-header-body">
                        <div>
                            <h2 class="admin-title">Dashboard CS</h2>
                            <p class="admin-subtitle">Buat tugas, pantau status teknisi, dan follow up pekerjaan yang belum selesai.</p>
                        </div>
                        <a href="{{ route('jobs.create') }}" class="btn-primary-soft">
                            <i class="fas fa-plus mr-2"></i>
                            Buat Tugas
                        </a>
                    </div>
                </div>

                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <div class="metric-card metric-sky">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Tugas Hari Ini</p>
                            <span class="metric-icon text-sky-600"><i class="fas fa-calendar-day"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'today', 0) }}</p>
                    </div>

                    <div class="metric-card metric-amber">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Aktif</p>
                            <span class="metric-icon text-amber-600"><i class="fas fa-spinner"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'active', 0) }}</p>
                    </div>

                    <div class="metric-card">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Pending</p>
                            <span class="metric-icon text-slate-500"><i class="fas fa-clock"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'pending', 0) }}</p>
                    </div>

                    <div class="metric-card metric-sky">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Proses</p>
                            <span class="metric-icon text-sky-600"><i class="fas fa-gears"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'process', 0) }}</p>
                    </div>

                    <div class="metric-card metric-emerald">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Selesai</p>
                            <span class="metric-icon text-emerald-600"><i class="fas fa-check"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'completed', 0) }}</p>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <div class="admin-card">
                        <div class="admin-card-header flex items-center justify-between gap-3">
                            <div>
                                <h3 class="admin-card-title">Tugas Aktif</h3>
                                <p class="mt-1 text-xs text-slate-500">Butuh follow up atau sedang dikerjakan teknisi.</p>
                            </div>
                            <a href="{{ route('jobs.history') }}" class="btn-secondary-soft">
                                <i class="fas fa-history mr-2"></i>
                                Riwayat
                            </a>
                        </div>
                        <div class="p-5 sm:p-6 space-y-3">
                            @forelse(($activeJobs ?? collect()) as $job)
                                <div class="rounded-xl border border-slate-200 bg-white p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-950 truncate">{{ $job->title }}</p>
                                            <p class="mt-1 text-xs text-slate-500 truncate">{{ $job->client_name ?: '-' }}</p>
                                        </div>
                                        <span class="app-badge {{ $job->status === 'process' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-800' }}">{{ $job->status }}</span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-500">
                                        <div>
                                            <span class="block font-semibold text-slate-700">Teknisi</span>
                                            {{ $job->technician?->name ?? '-' }}
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-slate-700">Divisi</span>
                                            {{ $job->technician?->division?->name ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="app-empty-state">
                                    <div class="app-empty-state-icon"><i class="fas fa-briefcase"></i></div>
                                    <p class="mt-3 font-semibold text-slate-900">Tidak ada tugas aktif</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Tugas Terbaru</h3>
                            <p class="mt-1 text-xs text-slate-500">15 tugas terakhir yang dibuat oleh CS ini.</p>
                        </div>
                        <div class="p-5 sm:p-6 space-y-3">
                            @forelse(($recentJobs ?? collect()) as $job)
                                <div class="rounded-xl border border-slate-200 bg-white p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-950 truncate">{{ $job->title }}</p>
                                            <p class="mt-1 text-xs text-slate-500 truncate">{{ $job->created_at?->format('d M Y H:i') ?? '-' }}</p>
                                        </div>
                                        <span class="app-badge-muted">{{ $job->status }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="app-empty-state">
                                    <div class="app-empty-state-icon"><i class="fas fa-list"></i></div>
                                    <p class="mt-3 font-semibold text-slate-900">Belum ada tugas</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>

    @elseif($dashboardRole === 'hr')
        <div class="admin-shell">
            <div class="admin-container">
                <div class="admin-page-header">
                    <div class="admin-page-header-accent"></div>
                    <div class="admin-page-header-body">
                        <div>
                            <h2 class="admin-title">Dashboard HR</h2>
                            <p class="admin-subtitle">Pantau rekrutmen, izin, dan ringkasan presensi.</p>
                        </div>
                        <a href="{{ route('recruitment.index') }}" class="btn-primary-soft">
                            <i class="fas fa-user-tie mr-2"></i>
                            Rekrutmen
                        </a>
                    </div>
                </div>

                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <div class="metric-card metric-emerald">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Lowongan</p>
                            <span class="metric-icon text-emerald-600"><i class="fas fa-briefcase"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'openings', 0) }}</p>
                    </div>

                    <div class="metric-card metric-sky">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Kandidat</p>
                            <span class="metric-icon text-sky-600"><i class="fas fa-users"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'candidates', 0) }}</p>
                    </div>

                    <div class="metric-card metric-amber">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Izin Pending</p>
                            <span class="metric-icon text-amber-600"><i class="fas fa-file-signature"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'pending_leaves', 0) }}</p>
                    </div>

                    <div class="metric-card">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Presensi Pending</p>
                            <span class="metric-icon text-slate-500"><i class="fas fa-calendar-check"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'pending_presence', 0) }}</p>
                    </div>
                </section>

                <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Lowongan Terbaru</h3>
                            <p class="mt-1 text-xs text-slate-500">10 data terakhir.</p>
                        </div>
                        <div class="p-5 sm:p-6 space-y-3">
                            @forelse(($openings ?? collect()) as $opening)
                                <div class="rounded-xl border border-slate-200 bg-white p-4">
                                    <p class="font-semibold text-slate-950">{{ $opening->title }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $opening->division }} · {{ $opening->status }}</p>
                                </div>
                            @empty
                                <div class="app-empty-state">
                                    <div class="app-empty-state-icon"><i class="fas fa-briefcase"></i></div>
                                    <p class="mt-3 font-semibold text-slate-900">Belum ada lowongan</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Kandidat Terbaru</h3>
                            <p class="mt-1 text-xs text-slate-500">10 data terakhir.</p>
                        </div>
                        <div class="p-5 sm:p-6 space-y-3">
                            @forelse(($candidates ?? collect()) as $candidate)
                                <div class="rounded-xl border border-slate-200 bg-white p-4">
                                    <p class="font-semibold text-slate-950">{{ $candidate->name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">{{ $candidate->position }} · {{ $candidate->stage }}</p>
                                </div>
                            @empty
                                <div class="app-empty-state">
                                    <div class="app-empty-state-icon"><i class="fas fa-users"></i></div>
                                    <p class="mt-3 font-semibold text-slate-900">Belum ada kandidat</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </div>

    @elseif($dashboardRole === 'finance')
        <div class="admin-shell">
            <div class="admin-container">
                <div class="admin-page-header">
                    <div class="admin-page-header-accent"></div>
                    <div class="admin-page-header-body">
                        <div>
                            <h2 class="admin-title">Dashboard Finance</h2>
                            <p class="admin-subtitle">Monitor progress pekerjaan untuk kebutuhan laporan dan kontrol operasional.</p>
                        </div>
                        <a href="{{ route('jobs.history') }}" class="btn-primary-soft">
                            <i class="fas fa-history mr-2"></i>
                            Riwayat Tugas
                        </a>
                    </div>
                </div>

                <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
                    <div class="metric-card">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Total</p>
                            <span class="metric-icon text-slate-500"><i class="fas fa-clipboard-list"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'total', 0) }}</p>
                    </div>
                    <div class="metric-card metric-amber">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Pending</p>
                            <span class="metric-icon text-amber-600"><i class="fas fa-clock"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'pending', 0) }}</p>
                    </div>
                    <div class="metric-card metric-sky">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Proses</p>
                            <span class="metric-icon text-sky-600"><i class="fas fa-spinner"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'process', 0) }}</p>
                    </div>
                    <div class="metric-card metric-emerald">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Selesai</p>
                            <span class="metric-icon text-emerald-600"><i class="fas fa-check"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'completed', 0) }}</p>
                    </div>
                    <div class="metric-card metric-rose">
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-semibold text-slate-500">Overdue (30 terbaru)</p>
                            <span class="metric-icon text-rose-600"><i class="fas fa-triangle-exclamation"></i></span>
                        </div>
                        <p class="mt-4 text-3xl font-bold text-slate-950">{{ (int) data_get($summary ?? [], 'overdue', 0) }}</p>
                    </div>
                </section>

                <section class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">30 Tugas Terakhir</h3>
                        <p class="mt-1 text-xs text-slate-500">Ringkasan cepat status dan deadline.</p>
                    </div>
                    <div class="p-5 sm:p-6 overflow-x-auto">
                        <table class="data-table admin-table-fixed">
                            <thead>
                                <tr>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Dibuat</th>
                                    <th>Deadline</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($jobs ?? collect()) as $job)
                                    <tr>
                                        <td class="font-semibold text-slate-900">{{ $job->title }}</td>
                                        <td><span class="app-badge-muted">{{ $job->status }}</span></td>
                                        <td class="text-slate-500">{{ $job->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="text-slate-500">{{ $job->end_time ? $job->end_time->format('d M Y H:i') : '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="app-empty-state">
                                                <div class="app-empty-state-icon"><i class="fas fa-clipboard-list"></i></div>
                                                <p class="mt-3 font-semibold text-slate-900">Belum ada data</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        </div>

    @else
        <div class="admin-shell">
            <div class="admin-container">
                <div class="admin-page-header">
                    <div class="admin-page-header-accent"></div>
                    <div class="admin-page-header-body">
                        <div>
                            <h2 class="admin-title">Dashboard Rangkuman Kerja</h2>
                            <p class="admin-subtitle">Pantau pekerjaan H-1, pekerjaan hari ini, dan tugas yang sedang berjalan.</p>
                        </div>

                        <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-semibold text-slate-700">
                            {{ now()->format('d M Y') }}
                        </div>
                    </div>
                </div>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="metric-card metric-emerald">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">Kerjaan Hari Ini</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                            <i class="fas fa-calendar-day"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ $summary['today_jobs'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Tugas dibuat hari ini</p>
                </div>

                <div class="metric-card metric-sky">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">Kerjaan H-1</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-sky-50 text-sky-600">
                            <i class="fas fa-clock-rotate-left"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ $summary['yesterday_jobs'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Tugas dibuat kemarin</p>
                </div>

                <div class="metric-card metric-amber">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">Sedang Berjalan</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                            <i class="fas fa-spinner"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ $summary['process_jobs'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Tugas dalam proses</p>
                </div>

                <div class="metric-card metric-rose">
                    <div class="flex items-center justify-between">
                        <p class="text-sm font-medium text-slate-500">Butuh Approval</p>
                        <span class="inline-flex h-10 w-10 items-center justify-center rounded-lg bg-rose-50 text-rose-600">
                            <i class="fas fa-clipboard-check"></i>
                        </span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-slate-950">{{ $summary['pending_presence_approvals'] + $summary['pending_leaves'] }}</p>
                    <p class="mt-1 text-xs text-slate-500">Absensi dan perizinan</p>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 xl:grid-cols-3">
                <div class="admin-card xl:col-span-2">
                    <div class="admin-card-header flex items-center justify-between gap-3">
                        <div>
                            <h3 class="admin-card-title">Pekerjaan Sekarang</h3>
                            <p class="mt-1 text-xs text-slate-500">Tugas pending dan proses yang perlu dipantau.</p>
                        </div>
                        <a href="{{ route('jobs.timeline') }}" class="text-sm font-semibold text-emerald-700 hover:text-emerald-900">Lihat timeline</a>
                    </div>

                    <div class="app-table-wrap rounded-none border-0 shadow-none">
                        <table class="data-table admin-table-fixed">
                            <colgroup>
                                <col class="w-[28%]">
                                <col class="w-[18%]">
                                <col class="w-[22%]">
                                <col class="w-[14%]">
                                <col class="w-[18%]">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th>Tugas</th>
                                    <th>Client</th>
                                    <th>Teknisi</th>
                                    <th>Status</th>
                                    <th>Deadline</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeJobs as $job)
                                    <tr class="data-table-row">
                                        <td>
                                            <div class="font-semibold text-slate-950">{{ $job->title }}</div>
                                            <div class="admin-table-text mt-1 text-xs">{{ $job->description ?: '-' }}</div>
                                        </td>
                                        <td class="text-slate-700">{{ $job->client_name ?: '-' }}</td>
                                        <td>
                                            <div class="font-medium text-slate-800">{{ $job->technician->name ?? '-' }}</div>
                                            <div class="mt-1 text-xs text-slate-500">{{ $job->technician->division->name ?? 'Tanpa Divisi' }}</div>
                                        </td>
                                        <td>
                                            <span class="app-badge {{ $job->status === 'process' ? 'bg-sky-100 text-sky-700' : 'app-badge-warning' }}">
                                                {{ $job->status }}
                                            </span>
                                            <div class="mt-1 text-xs text-slate-500">Step {{ $job->current_step }}</div>
                                        </td>
                                        <td class="text-slate-700">
                                            {{ $job->end_time ? $job->end_time->format('d M Y H:i') : '-' }}
                                            @if($job->is_overdue)
                                                <div class="mt-1 text-xs font-semibold text-rose-600">Overdue</div>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="app-empty-state">
                                                <div class="app-empty-state-icon"><i class="fas fa-briefcase"></i></div>
                                                <p class="mt-3 font-semibold text-slate-900">Tidak ada pekerjaan aktif sekarang</p>
                                                <p class="mt-1 text-sm text-slate-500">Tugas pending dan proses akan muncul di sini.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Status Kerja</h3>
                        </div>
                        <div class="space-y-4 p-5">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Pending</span>
                                <span class="app-badge-warning">{{ $summary['pending_jobs'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Proses</span>
                                <span class="app-badge bg-sky-100 text-sky-700">{{ $summary['process_jobs'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Selesai</span>
                                <span class="app-badge-success">{{ $summary['completed_jobs'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Overdue</span>
                                <span class="app-badge bg-rose-100 text-rose-700">{{ $summary['overdue_jobs'] }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Operasional Hari Ini</h3>
                        </div>
                        <div class="space-y-4 p-5">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Karyawan</span>
                                <span class="font-semibold text-slate-950">{{ $summary['employees'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Presensi Hari Ini</span>
                                <span class="font-semibold text-slate-950">{{ $summary['today_attendance'] }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Approval Absensi</span>
                                <a href="{{ route('admin.presence.index') }}" class="font-semibold text-emerald-700 hover:text-emerald-900">{{ $summary['pending_presence_approvals'] }}</a>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-slate-500">Perizinan Pending</span>
                                <a href="{{ route('admin.perizinan') }}" class="font-semibold text-emerald-700 hover:text-emerald-900">{{ $summary['pending_leaves'] }}</a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 xl:grid-cols-2">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Kerjaan Hari Ini</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($todayJobs as $job)
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-950">{{ $job->title }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $job->client_name ?: 'Tanpa client' }} · {{ $job->technician->name ?? '-' }}</p>
                                    </div>
                                    <span class="app-badge {{ $job->status === 'completed' ? 'app-badge-success' : ($job->status === 'process' ? 'bg-sky-100 text-sky-700' : 'app-badge-warning') }}">{{ $job->status }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="app-empty-state">
                                <div class="app-empty-state-icon"><i class="fas fa-calendar-day"></i></div>
                                <p class="mt-3 font-semibold text-slate-900">Belum ada tugas hari ini</p>
                                <p class="mt-1 text-sm text-slate-500">Tugas yang dibuat hari ini akan muncul di daftar ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Kerjaan H-1</h3>
                    </div>
                    <div class="divide-y divide-slate-100">
                        @forelse($yesterdayJobs as $job)
                            <div class="p-5">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <p class="font-semibold text-slate-950">{{ $job->title }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $job->client_name ?: 'Tanpa client' }} · {{ $job->technician->name ?? '-' }}</p>
                                    </div>
                                    <span class="app-badge {{ $job->status === 'completed' ? 'app-badge-success' : ($job->status === 'process' ? 'bg-sky-100 text-sky-700' : 'app-badge-warning') }}">{{ $job->status }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="app-empty-state">
                                <div class="app-empty-state-icon"><i class="fas fa-clock-rotate-left"></i></div>
                                <p class="mt-3 font-semibold text-slate-900">Tidak ada tugas H-1</p>
                                <p class="mt-1 text-sm text-slate-500">Tugas yang dibuat kemarin akan muncul di daftar ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
    @endif
</x-app-layout>
