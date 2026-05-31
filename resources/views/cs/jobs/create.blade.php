<x-app-layout>
    @php
        $userRole = strtolower((string) (Auth::user()->role ?? ''));
        if (in_array($userRole, ['admin', 'kepala'], true)) {
            $userRole = 'administrator';
        }

        $canCreate = in_array($userRole, ['cs', 'administrator'], true);
    @endphp

    <div class="admin-shell" x-data="{ createOpen: @js($errors->any()), search: '' }">
        <div class="admin-container">
            <div class="admin-page-header">
                <div class="admin-page-header-accent"></div>
                <div class="admin-page-header-body">
                    <div>
                        <h2 class="admin-title">Buat Surat Jalan</h2>
                        <p class="admin-subtitle">Lengkapi data customer, pilih teknisi, lalu kirim surat jalan untuk dikerjakan di lapangan.</p>
                    </div>

                    <a href="{{ route('jobs.history') }}" class="btn-secondary-soft">
                        <i class="fas fa-clock-rotate-left mr-2"></i>
                        Riwayat
                    </a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert-success flex items-center justify-between gap-4">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button type="button" onclick="this.closest('.alert-success').remove()" class="text-emerald-700 hover:text-emerald-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error flex items-center justify-between gap-4">
                    <span><i class="fas fa-circle-exclamation mr-2"></i>{{ session('error') }}</span>
                    <button type="button" onclick="this.closest('.alert-error').remove()" class="text-rose-700 hover:text-rose-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <p class="font-semibold">Tugas belum bisa dikirim.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-5 xl:grid-cols-[1fr_420px]">
                <section class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Form Tugas</h3>
                        <p class="mt-1 text-xs text-slate-500">Field penting: judul tugas, teknisi, deadline, dan kontak WhatsApp.</p>
                    </div>

                    <div class="p-5 sm:p-6">
                        @if(!$canCreate)
                            <div class="alert-error">
                                Role Anda tidak memiliki akses untuk membuat tugas.
                            </div>
                        @else
                            <form action="{{ route('jobs.store') }}" method="POST" class="space-y-4" data-submit-lock>
                                @csrf

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <label class="block">
                                        <span class="field-label">Judul Tugas</span>
                                        <x-text-input name="title" value="{{ old('title') }}" class="form-control" placeholder="Contoh: Pasang ONT / Trouble koneksi" required />
                                    </label>

                                    <label class="block">
                                        <span class="field-label">Teknisi</span>
                                        <select name="technician_id" class="form-control" required>
                                            <option value="">Pilih teknisi</option>
                                            @foreach($technicians as $tech)
                                                <option value="{{ $tech->id }}" @selected(old('technician_id') == $tech->id)>
                                                    {{ $tech->name }}{{ $tech->division?->name ? ' · '.$tech->division->name : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </label>
                                </div>

                                <label class="block">
                                    <span class="field-label">Deskripsi</span>
                                    <textarea name="description" rows="3" class="form-control" placeholder="Rincian masalah / pekerjaan (opsional)">{{ old('description') }}</textarea>
                                </label>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <label class="block">
                                        <span class="field-label">Nama Client</span>
                                        <x-text-input name="client_name" value="{{ old('client_name') }}" class="form-control" placeholder="Nama pelanggan / perusahaan" />
                                    </label>
                                    <label class="block">
                                        <span class="field-label">WhatsApp</span>
                                        <x-text-input name="whatsapp_number" value="{{ old('whatsapp_number') }}" class="form-control" placeholder="08xxxxxxxxxx" />
                                    </label>
                                </div>

                                <label class="block">
                                    <span class="field-label">Lokasi</span>
                                    <x-text-input name="location" value="{{ old('location') }}" class="form-control" placeholder="Alamat singkat / patokan" />
                                </label>

                                <label class="block">
                                    <span class="field-label">Link Google Maps</span>
                                    <x-text-input name="google_maps_link" value="{{ old('google_maps_link') }}" class="form-control" placeholder="https://maps.google.com/..." />
                                </label>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <label class="block">
                                        <span class="field-label">Waktu Mulai</span>
                                        <x-text-input name="start_time" type="datetime-local" value="{{ old('start_time') }}" class="form-control" />
                                    </label>
                                    <label class="block">
                                        <span class="field-label">Deadline</span>
                                        <x-text-input name="end_time" type="datetime-local" value="{{ old('end_time') }}" class="form-control" />
                                    </label>
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-2">
                                    <a href="{{ route('jobs.history') }}" class="btn-secondary-soft">Batal</a>
                                    <button type="submit" class="btn-primary-soft">
                                        <i class="fas fa-paper-plane mr-2"></i>
                                        Kirim ke Teknisi
                                    </button>
                                </div>
                            </form>
                        @endif
                    </div>
                </section>

                <aside class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title">Tugas Terbaru</h3>
                        <p class="mt-1 text-xs text-slate-500">Untuk memastikan tugas sudah masuk ke daftar.</p>
                    </div>
                    <div class="p-5 sm:p-6 space-y-3">
                        <div class="relative">
                            <i class="fas fa-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                            <input x-model="search" type="text" class="form-control pl-9" placeholder="Cari judul / client / teknisi" />
                        </div>

                        <div class="space-y-2 max-h-[520px] overflow-y-auto pr-1">
                            @forelse($jobs->take(25) as $job)
                                @php
                                    $statusClass = $job->status === 'completed'
                                        ? 'bg-emerald-100 text-emerald-700'
                                        : ($job->status === 'process' ? 'bg-sky-100 text-sky-700' : 'bg-amber-100 text-amber-800');
                                    $searchText = strtolower(
                                        ($job->title ?? '').' '.($job->client_name ?? '').' '.($job->technician?->name ?? '')
                                    );
                                @endphp
                                <div x-show="search.trim() === '' || @js($searchText).includes(search.trim().toLowerCase())" class="rounded-xl border border-slate-200 bg-white p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-950 truncate">{{ $job->title }}</p>
                                            <p class="mt-1 text-xs text-slate-500 truncate">{{ $job->client_name ?: 'Client belum diisi' }}</p>
                                        </div>
                                        <span class="app-badge {{ $statusClass }}">{{ $job->status }}</span>
                                    </div>
                                    <div class="mt-3 grid grid-cols-2 gap-2 text-xs text-slate-500">
                                        <div>
                                            <span class="block font-semibold text-slate-700">Teknisi</span>
                                            {{ $job->technician?->name ?? '-' }}
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-slate-700">Dibuat</span>
                                            {{ $job->created_at?->format('d M H:i') ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="app-empty-state">
                                    <div class="app-empty-state-icon"><i class="fas fa-briefcase"></i></div>
                                    <p class="mt-3 font-semibold text-slate-900">Belum ada tugas</p>
                                    <p class="mt-1 text-sm text-slate-500">Mulai dengan membuat tugas pertama.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </div>
</x-app-layout>
