<x-app-layout>
    @php
        $hasCheckedIn = $todayPresence !== null;
        $hasCheckedOut = $todayPresence?->check_out !== null;
    @endphp

    <div class="admin-shell">
        <div class="admin-container">
            <div class="admin-page-header">
                <div class="admin-page-header-accent"></div>
                <div class="admin-page-header-body">
                    <div>
                        <h2 class="admin-title">Dashboard Karyawan</h2>
                        <p class="admin-subtitle">Ringkasan absensi, tugas hari ini, chat, dan notifikasi.</p>
                    </div>
                </div>
            </div>

            <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
                <div class="metric-card {{ $hasCheckedIn ? 'metric-emerald' : 'metric-amber' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Absensi Hari Ini</p>
                            <p class="mt-3 text-2xl font-bold {{ $hasCheckedIn ? 'text-emerald-700' : 'text-amber-700' }}">
                                {{ $hasCheckedIn ? 'Sudah Masuk' : 'Belum Masuk' }}
                            </p>
                        </div>
                        <span class="metric-icon {{ $hasCheckedIn ? 'text-emerald-600' : 'text-amber-600' }}">
                            <i class="fas fa-fingerprint"></i>
                        </span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">
                        {{ $hasCheckedIn ? 'Masuk '.$todayPresence->check_in.($hasCheckedOut ? ' · Pulang '.$todayPresence->check_out : '') : 'Silakan lakukan check-in terlebih dahulu.' }}
                    </p>
                </div>

                <div class="metric-card metric-sky">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Tugas Hari Ini</p>
                            <p class="mt-3 text-3xl font-bold text-sky-700">{{ (int) $todayTasks }}</p>
                        </div>
                        <span class="metric-icon text-sky-600"><i class="fas fa-briefcase"></i></span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Tugas dengan start/deadline hari ini</p>
                </div>

                <div class="metric-card metric-amber">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Chat Belum Dibaca</p>
                            <p class="mt-3 text-3xl font-bold text-amber-700">{{ (int) $newMessages }}</p>
                        </div>
                        <span class="metric-icon text-amber-600"><i class="fas fa-comments"></i></span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Pesan baru yang belum terbaca</p>
                </div>

                <div class="metric-card metric-rose">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-sm font-semibold text-slate-500">Notifikasi</p>
                            <p class="mt-3 text-3xl font-bold text-rose-700">{{ (int) $notificationsCount }}</p>
                        </div>
                        <span class="metric-icon text-rose-600"><i class="fas fa-bell"></i></span>
                    </div>
                    <p class="mt-4 text-xs text-slate-500">Belum dibaca</p>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-4 xl:grid-cols-2">
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Ringkasan Bulan Ini</h3>
                        <p class="mt-1 text-xs text-slate-500">Statistik absensi bulan berjalan.</p>
                    </div>
                    <div class="p-5 sm:p-6 grid grid-cols-2 gap-4">
                        <div class="app-surface p-5">
                            <p class="text-sm font-medium text-slate-500">Hadir</p>
                            <p class="mt-3 text-2xl font-bold text-slate-950">{{ (int) data_get($attendanceStats, 'present', 0) }}</p>
                        </div>
                        <div class="app-surface p-5">
                            <p class="text-sm font-medium text-slate-500">Terlambat</p>
                            <p class="mt-3 text-2xl font-bold text-slate-950">{{ (int) data_get($attendanceStats, 'late', 0) }}</p>
                        </div>
                        <div class="app-surface p-5">
                            <p class="text-sm font-medium text-slate-500">Izin</p>
                            <p class="mt-3 text-2xl font-bold text-slate-950">{{ (int) data_get($attendanceStats, 'permit', 0) }}</p>
                        </div>
                        <div class="app-surface p-5">
                            <p class="text-sm font-medium text-slate-500">Absen</p>
                            <p class="mt-3 text-2xl font-bold text-slate-950">{{ (int) data_get($attendanceStats, 'absent', 0) }}</p>
                        </div>
                    </div>
                </div>

                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Aktivitas Terbaru</h3>
                        <p class="mt-1 text-xs text-slate-500">4 notifikasi terakhir.</p>
                    </div>
                    <div class="p-5 sm:p-6 space-y-3">
                        @forelse($news as $item)
                            <div class="rounded-xl border border-slate-200 bg-white p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="min-w-0">
                                        <p class="font-semibold text-slate-950 truncate">{{ $item->title }}</p>
                                        <p class="mt-1 text-xs text-slate-500 truncate">{{ $item->created_at?->format('d M Y H:i') ?? '-' }}</p>
                                    </div>
                                    <span class="app-badge-muted">Info</span>
                                </div>
                                <p class="mt-2 text-sm text-slate-600 line-clamp-2">{{ data_get($item->data, 'message', '') }}</p>
                            </div>
                        @empty
                            <div class="app-empty-state">
                                <div class="app-empty-state-icon"><i class="fas fa-bell-slash"></i></div>
                                <p class="mt-3 font-semibold text-slate-900">Belum ada notifikasi</p>
                                <p class="mt-1 text-sm text-slate-500">Aktivitas akan muncul di sini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-app-layout>

