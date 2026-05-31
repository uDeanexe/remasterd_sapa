<x-app-layout>
    <div class="admin-shell">
        <div class="admin-container">
            <div class="admin-page-header">
                <div class="admin-page-header-accent"></div>
                <div class="admin-page-header-body">
                    <div>
                        <h2 class="admin-title">Profil</h2>
                        <p class="admin-subtitle">Ringkasan performa dan aktivitas akun.</p>
                    </div>

                    <a href="{{ route('profile.edit') }}" class="btn-secondary-soft">
                        <i class="fas fa-user-pen mr-2"></i>
                        Edit Profil
                    </a>
                </div>
            </div>

            <section class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <div class="metric-card metric-emerald">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Tugas Selesai</p>
                            <p class="mt-3 text-3xl font-bold text-emerald-700">{{ (int) data_get($stats, 'completed_tasks', 0) }}</p>
                        </div>
                        <span class="metric-icon text-emerald-600"><i class="fas fa-badge-check"></i></span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Total tugas dengan status completed</p>
                </div>

                <div class="metric-card metric-sky">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Tugas Aktif</p>
                            <p class="mt-3 text-3xl font-bold text-sky-700">{{ (int) data_get($stats, 'active_tasks', 0) }}</p>
                        </div>
                        <span class="metric-icon text-sky-600"><i class="fas fa-spinner"></i></span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Sedang dikerjakan</p>
                </div>

                <div class="metric-card metric-amber">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Absensi Bulan Ini</p>
                            <p class="mt-3 text-3xl font-bold text-amber-700">{{ (int) data_get($stats, 'monthly_attendance', 0) }}</p>
                        </div>
                        <span class="metric-icon text-amber-600"><i class="fas fa-calendar-check"></i></span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Jumlah record presensi</p>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

