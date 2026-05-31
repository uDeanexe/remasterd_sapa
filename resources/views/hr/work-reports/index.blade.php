<x-app-layout>
    <div class="admin-shell">
        <div class="admin-container">
            <div class="admin-page-header">
                <div class="admin-page-header-accent"></div>
                <div class="admin-page-header-body">
                    <div>
                        <h2 class="admin-title">Rekap Laporan Kerja</h2>
                        <p class="admin-subtitle">Monitoring pekerjaan teknisi untuk input KPI: job selesai, overdue, durasi, dan catatan lapangan.</p>
                    </div>
                </div>
            </div>

            <section class="admin-card">
                <div class="admin-card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="admin-card-title">Filter Periode</h3>
                        <p class="mt-1 text-xs text-slate-500">Pilih bulan, tahun, dan (opsional) teknisi.</p>
                    </div>
                </div>
                <form method="GET" action="{{ route('hr.work-reports.index') }}" class="admin-card-body grid grid-cols-1 gap-4 md:grid-cols-3" data-submit-lock>
                    <label>
                        <span class="field-label">Bulan</span>
                        <select name="month" class="form-control">
                            @foreach($months as $idx => $label)
                                <option value="{{ $idx + 1 }}" @selected((int) $month === (int) ($idx + 1))>{{ $label }}</option>
                            @endforeach
                        </select>
                    </label>
                    <label>
                        <span class="field-label">Tahun</span>
                        <x-text-input name="year" type="number" min="2000" max="2100" value="{{ $year }}" class="form-control" />
                    </label>
                    <label>
                        <span class="field-label">Teknisi (opsional)</span>
                        <select name="user_id" class="form-control">
                            <option value="">Semua teknisi</option>
                            @foreach($technicians as $t)
                                <option value="{{ $t->id }}" @selected((int) $userId === (int) $t->id)>{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </label>
                    <div class="md:col-span-3 flex justify-end">
                        <button type="submit" class="btn-primary-soft">Tampilkan</button>
                    </div>
                </form>
            </section>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="metric-card metric-emerald">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-500">Job Selesai</p>
                        <span class="metric-icon text-emerald-600"><i class="fas fa-check-circle"></i></span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-emerald-700">{{ $summary['completed_jobs'] ?? 0 }}</p>
                </div>
                <div class="metric-card metric-rose">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-500">Overdue</p>
                        <span class="metric-icon text-rose-600"><i class="fas fa-triangle-exclamation"></i></span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-rose-700">{{ $summary['overdue_jobs'] ?? 0 }}</p>
                </div>
                <div class="metric-card metric-sky">
                    <div class="flex items-center justify-between gap-3">
                        <p class="text-sm font-semibold text-slate-500">Rata-rata Durasi</p>
                        <span class="metric-icon text-sky-600"><i class="fas fa-clock"></i></span>
                    </div>
                    <p class="mt-3 text-3xl font-bold text-sky-700">{{ $summary['avg_duration_minutes'] ?? 0 }}m</p>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Laporan Harian</h3>
                    <p class="mt-1 text-xs text-slate-500">Catatan lapangan teknisi per hari. Bisa dipakai sebagai bahan KPI (Manual/Task).</p>
                </div>
                <div class="admin-card-body">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Teknisi</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $row)
                                    <tr class="data-table-row">
                                        <td class="min-w-40 text-slate-600">{{ $row->report_date?->translatedFormat('d M Y') }}</td>
                                        <td class="min-w-56 font-semibold text-slate-900">{{ $row->user?->name ?? '-' }}</td>
                                        <td class="min-w-[24rem] text-slate-600">{{ $row->note ?: '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3">
                                            <div class="app-empty-state">
                                                <div class="app-empty-state-icon"><i class="fas fa-clipboard"></i></div>
                                                <p class="mt-3 font-semibold text-slate-900">Belum ada laporan</p>
                                                <p class="mt-1 text-sm text-slate-500">Teknisi bisa mengisi lewat menu Laporan Kerja.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            <section class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title">Detail Job Selesai</h3>
                    <p class="mt-1 text-xs text-slate-500">Data kerja yang sudah selesai pada periode ini.</p>
                </div>
                <div class="admin-card-body">
                    <div class="overflow-x-auto">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Job</th>
                                    <th>Teknisi</th>
                                    <th>Selesai</th>
                                    <th>Durasi</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($completedJobs as $job)
                                    <tr class="data-table-row">
                                        <td class="min-w-72">
                                            <p class="font-semibold text-slate-950">{{ $job->title }}</p>
                                            <p class="mt-1 text-xs text-slate-500">{{ $job->client_name ?: '-' }}</p>
                                        </td>
                                        <td class="min-w-56 text-slate-600">{{ $job->technician?->name ?? '-' }}</td>
                                        <td class="min-w-40 text-slate-600">{{ $job->completed_at ? $job->completed_at->translatedFormat('d M Y H:i') : '-' }}</td>
                                        <td class="min-w-28 text-slate-600">{{ $job->actual_duration_label ?? '-' }}</td>
                                        <td class="min-w-28">
                                            <span class="{{ $job->is_overdue ? 'app-badge bg-rose-100 text-rose-700' : 'app-badge-success' }}">
                                                {{ $job->is_overdue ? 'Overdue' : 'On time' }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-slate-600">Tidak ada job selesai pada periode ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

