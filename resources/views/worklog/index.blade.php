<x-app-layout>
    @php
        $monthStart = $monthStart ?? now()->startOfMonth();
        $monthLabel = $monthStart->format('F Y');
        $prevMonth = $monthStart->copy()->subMonth();
        $nextMonth = $monthStart->copy()->addMonth();
        $timelineByStatus = collect($timelineItems ?? [])->groupBy(fn ($row) => (string) ($row->status ?? 'planned'));
    @endphp

    <div class="admin-shell" x-data="{ tab: @js($tab), addDate: @js(now()->toDateString()) }">
        <div class="admin-container">
            <div class="admin-page-header">
                <div class="admin-page-header-accent"></div>
                <div class="admin-page-header-body">
                    <div>
                        <h2 class="admin-title">Timeline & Catatan Kerja</h2>
                        <p class="admin-subtitle">Timeline, note, dan report kerja untuk dokumentasi aktivitas. @if($viewAll) Mode monitor aktif (HR/Admin). @endif</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div id="worklog-success" class="alert-success flex items-center justify-between gap-4">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button type="button" data-dismiss="#worklog-success" class="text-emerald-700 hover:text-emerald-900">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <p class="font-semibold">Data belum bisa disimpan.</p>
                    <ul class="mt-2 list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="admin-card">
                <div class="admin-card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="admin-card-title">Worklog</h3>
                        <p class="mt-1 text-xs text-slate-500">Catat aktivitas harian, kendala, dan laporan periodik.</p>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <a href="{{ route('worklog.index', ['tab' => 'timeline']) }}" class="btn-secondary-soft {{ $tab==='timeline' ? 'ring-2 ring-emerald-200' : '' }}">Timeline</a>
                        <a href="{{ route('worklog.index', ['tab' => 'notes']) }}" class="btn-secondary-soft {{ $tab==='notes' ? 'ring-2 ring-emerald-200' : '' }}">Note</a>
                        <a href="{{ route('worklog.index', ['tab' => 'reports']) }}" class="btn-secondary-soft {{ $tab==='reports' ? 'ring-2 ring-emerald-200' : '' }}">Report</a>
                    </div>
                </div>

                <div class="admin-card-body space-y-6">
                    @if($tab === 'timeline')
                        <div class="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('worklog.index', ['tab' => 'timeline', 'month' => $prevMonth->month, 'year' => $prevMonth->year, 'q' => $q]) }}" class="btn-secondary-soft" aria-label="Bulan sebelumnya">
                                    <i class="fas fa-chevron-left"></i>
                                </a>
                                <div class="rounded-xl border border-slate-200 bg-slate-50 px-4 py-2 text-sm font-bold text-slate-800">
                                    {{ $monthLabel }}
                                </div>
                                <a href="{{ route('worklog.index', ['tab' => 'timeline', 'month' => $nextMonth->month, 'year' => $nextMonth->year, 'q' => $q]) }}" class="btn-secondary-soft" aria-label="Bulan berikutnya">
                                    <i class="fas fa-chevron-right"></i>
                                </a>
                                <a href="{{ route('worklog.index', ['tab' => 'timeline', 'month' => now()->month, 'year' => now()->year]) }}" class="btn-secondary-soft">Today</a>
                            </div>

                            <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                                <form method="GET" action="{{ route('worklog.index') }}" class="flex items-center gap-2">
                                    <input type="hidden" name="tab" value="timeline" />
                                    <input type="hidden" name="month" value="{{ $monthStart->month }}" />
                                    <input type="hidden" name="year" value="{{ $monthStart->year }}" />
                                    <x-text-input name="q" value="{{ $q ?? '' }}" class="form-control w-full sm:w-80" placeholder="Filter by keyword..." />
                                    <button type="submit" class="btn-secondary-soft">View</button>
                                </form>
                                <button
                                    type="button"
                                    class="btn-primary-soft"
                                    @click="$dispatch('open-modal', 'worklog-add-timeline'); addDate = '{{ now()->toDateString() }}';"
                                >
                                    <i class="fas fa-plus mr-2"></i> Add item
                                </button>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-4">
                            @php
                                $columns = [
                                    ['key' => 'planned', 'label' => 'Planned', 'color' => 'bg-slate-400'],
                                    ['key' => 'in_progress', 'label' => 'In Progress', 'color' => 'bg-blue-500'],
                                    ['key' => 'done', 'label' => 'Done', 'color' => 'bg-emerald-500'],
                                    ['key' => 'blocked', 'label' => 'Blocked', 'color' => 'bg-rose-500'],
                                ];
                            @endphp

                            @foreach($columns as $col)
                                @php
                                    $items = $timelineByStatus->get($col['key'], collect());
                                @endphp
                                <div class="rounded-2xl border border-slate-200 bg-white overflow-hidden">
                                    <div class="flex items-center justify-between border-b border-slate-200 bg-slate-50 px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <span class="h-2.5 w-2.5 rounded-full {{ $col['color'] }}"></span>
                                            <p class="text-sm font-bold text-slate-800">{{ $col['label'] }}</p>
                                        </div>
                                        <span class="app-badge-muted">{{ $items->count() }}</span>
                                    </div>
                                    <div class="p-3 space-y-2">
                                        @forelse($items as $row)
                                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-bold text-slate-700">{{ $row->work_date?->format('Y-m-d') }}</p>
                                                        <p class="mt-1 font-semibold text-slate-900 truncate">{{ $row->title }}</p>
                                                        @if($viewAll)
                                                            <p class="mt-1 text-xs text-slate-500 truncate">{{ $row->user?->name ?? '-' }}</p>
                                                        @endif
                                                    </div>
                                                    <form method="POST" action="{{ route('worklog.timeline.destroy', $row) }}" data-confirm="Hapus item timeline ini?">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-rose-600 hover:text-rose-800" title="Hapus">
                                                            <i class="fas fa-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                                @if($row->description)
                                                    <p class="mt-2 text-xs text-slate-600 line-clamp-3">{{ $row->description }}</p>
                                                @endif
                                            </div>
                                        @empty
                                            <div class="rounded-xl border border-dashed border-slate-200 bg-white px-3 py-8 text-center text-sm text-slate-500">
                                                Kosong
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <x-modal name="worklog-add-timeline" :show="false" maxWidth="2xl" focusable>
                            <div class="bg-slate-900 text-white">
                                <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
                                    <div>
                                        <h3 class="text-lg font-bold">Add Timeline Item</h3>
                                        <p class="mt-1 text-xs text-white/70">Isi aktivitas kerja harian.</p>
                                    </div>
                                    <button type="button" class="text-white/70 hover:text-white" @click="$dispatch('close-modal', 'worklog-add-timeline')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('worklog.timeline.store') }}" class="space-y-4 px-6 py-5" data-submit-lock>
                                    @csrf
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <label>
                                            <span class="field-label !text-white/80">Tanggal</span>
                                            <x-text-input name="work_date" type="date" x-model="addDate" class="form-control" required />
                                        </label>
                                        <label>
                                            <span class="field-label !text-white/80">Status</span>
                                            <select name="status" class="form-control" required>
                                                @foreach(['planned'=>'Planned','in_progress'=>'In Progress','done'=>'Done','blocked'=>'Blocked'] as $v=>$l)
                                                    <option value="{{ $v }}" @selected(old('status','planned')===$v)>{{ $l }}</option>
                                                @endforeach
                                            </select>
                                        </label>
                                        <label class="md:col-span-2">
                                            <span class="field-label !text-white/80">Judul</span>
                                            <x-text-input name="title" value="{{ old('title') }}" class="form-control" placeholder="Judul aktivitas..." required />
                                        </label>
                                        <label class="md:col-span-2">
                                            <span class="field-label !text-white/80">Deskripsi</span>
                                            <textarea name="description" rows="4" class="form-control" placeholder="Catatan aktivitas, progres, kendala...">{{ old('description') }}</textarea>
                                        </label>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" class="btn-secondary-soft" @click="$dispatch('close-modal', 'worklog-add-timeline')">Batal</button>
                                        <button type="submit" class="btn-primary-soft">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                    @elseif($tab === 'notes')
                        <div class="flex justify-end">
                            <button type="button" class="btn-primary-soft" @click="$dispatch('open-modal','worklog-add-note')">
                                <i class="fas fa-plus mr-2"></i> Add note
                            </button>
                        </div>

                        <div class="space-y-3">
                            @forelse($notes as $row)
                                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">{{ $row->note_date?->format('Y-m-d') }}</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900">{{ $row->title }}</p>
                                            @if($viewAll)
                                                <p class="mt-1 text-xs text-slate-500">Oleh: {{ $row->user?->name ?? '-' }}</p>
                                            @endif
                                            @if($row->tags)
                                                <p class="mt-2 text-xs text-slate-500">Tags: {{ $row->tags }}</p>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('worklog.notes.destroy', $row) }}" data-confirm="Hapus note ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger-soft">Hapus</button>
                                        </form>
                                    </div>
                                    <pre class="whitespace-pre-wrap text-sm leading-6 text-slate-700 bg-slate-50 rounded-xl p-4 border border-slate-200 mt-4">{{ $row->body }}</pre>
                                </div>
                            @empty
                                <div class="app-empty-state py-12">Belum ada note kerja.</div>
                            @endforelse
                        </div>

                        <x-modal name="worklog-add-note" :show="false" maxWidth="2xl" focusable>
                            <div class="bg-slate-900 text-white">
                                <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
                                    <div>
                                        <h3 class="text-lg font-bold">Add Note</h3>
                                        <p class="mt-1 text-xs text-white/70">Catatan aktivitas, kendala, follow-up, koordinasi.</p>
                                    </div>
                                    <button type="button" class="text-white/70 hover:text-white" @click="$dispatch('close-modal', 'worklog-add-note')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('worklog.notes.store') }}" class="space-y-4 px-6 py-5" data-submit-lock>
                                    @csrf
                                    <label>
                                        <span class="field-label !text-white/80">Tanggal</span>
                                        <x-text-input name="note_date" type="date" value="{{ old('note_date', now()->toDateString()) }}" class="form-control" required />
                                    </label>
                                    <label>
                                        <span class="field-label !text-white/80">Judul</span>
                                        <x-text-input name="title" value="{{ old('title') }}" class="form-control" required />
                                    </label>
                                    <label>
                                        <span class="field-label !text-white/80">Isi Note</span>
                                        <textarea name="body" rows="6" class="form-control" required>{{ old('body') }}</textarea>
                                    </label>
                                    <label>
                                        <span class="field-label !text-white/80">Tags (opsional)</span>
                                        <x-text-input name="tags" value="{{ old('tags') }}" class="form-control" />
                                    </label>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" class="btn-secondary-soft" @click="$dispatch('close-modal', 'worklog-add-note')">Batal</button>
                                        <button type="submit" class="btn-primary-soft">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                    @else
                        <div class="flex justify-end">
                            <button type="button" class="btn-primary-soft" @click="$dispatch('open-modal','worklog-add-report')">
                                <i class="fas fa-plus mr-2"></i> Add report
                            </button>
                        </div>

                        <div class="space-y-3">
                            @forelse($reports as $row)
                                <div class="rounded-2xl border border-slate-200 bg-white p-5">
                                    <div class="flex flex-wrap items-start justify-between gap-3">
                                        <div>
                                            <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Periode {{ $row->period_start?->format('Y-m-d') }} s/d {{ $row->period_end?->format('Y-m-d') }}</p>
                                            <p class="mt-1 text-lg font-bold text-slate-900">{{ $row->title }}</p>
                                            @if($viewAll)
                                                <p class="mt-1 text-xs text-slate-500">Oleh: {{ $row->user?->name ?? '-' }}</p>
                                            @endif
                                        </div>
                                        <form method="POST" action="{{ route('worklog.reports.destroy', $row) }}" data-confirm="Hapus report ini?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-danger-soft">Hapus</button>
                                        </form>
                                    </div>
                                    <pre class="whitespace-pre-wrap text-sm leading-6 text-slate-700 bg-slate-50 rounded-xl p-4 border border-slate-200 mt-4">{{ $row->summary }}</pre>
                                </div>
                            @empty
                                <div class="app-empty-state py-12">Belum ada report kerja.</div>
                            @endforelse
                        </div>

                        <x-modal name="worklog-add-report" :show="false" maxWidth="2xl" focusable>
                            <div class="bg-slate-900 text-white">
                                <div class="flex items-center justify-between border-b border-white/10 px-6 py-4">
                                    <div>
                                        <h3 class="text-lg font-bold">Add Report</h3>
                                        <p class="mt-1 text-xs text-white/70">Ringkasan kerja periodik untuk monitoring.</p>
                                    </div>
                                    <button type="button" class="text-white/70 hover:text-white" @click="$dispatch('close-modal', 'worklog-add-report')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <form method="POST" action="{{ route('worklog.reports.store') }}" class="space-y-4 px-6 py-5" data-submit-lock>
                                    @csrf
                                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                        <label>
                                            <span class="field-label !text-white/80">Periode Mulai</span>
                                            <x-text-input name="period_start" type="date" value="{{ old('period_start', now()->startOfMonth()->toDateString()) }}" class="form-control" required />
                                        </label>
                                        <label>
                                            <span class="field-label !text-white/80">Periode Selesai</span>
                                            <x-text-input name="period_end" type="date" value="{{ old('period_end', now()->toDateString()) }}" class="form-control" required />
                                        </label>
                                        <label class="md:col-span-2">
                                            <span class="field-label !text-white/80">Judul</span>
                                            <x-text-input name="title" value="{{ old('title') }}" class="form-control" required />
                                        </label>
                                        <label class="md:col-span-2">
                                            <span class="field-label !text-white/80">Ringkasan</span>
                                            <textarea name="summary" rows="8" class="form-control" required>{{ old('summary') }}</textarea>
                                        </label>
                                    </div>
                                    <div class="flex justify-end gap-2 pt-2">
                                        <button type="button" class="btn-secondary-soft" @click="$dispatch('close-modal', 'worklog-add-report')">Batal</button>
                                        <button type="submit" class="btn-primary-soft">Simpan</button>
                                    </div>
                                </form>
                            </div>
                        </x-modal>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
