<x-app-layout>
    <div class="admin-shell" x-data="{ tab: @js($tab) }">
        <div class="admin-container">
            <div class="admin-page-header">
                <div class="admin-page-header-accent"></div>
                <div class="admin-page-header-body">
                    <div>
                        <h2 class="admin-title">Pengaturan Sistem</h2>
                        <p class="admin-subtitle">Kelola setting user, absensi, pekerjaan, dan konfigurasi lainnya dengan aman.</p>
                    </div>
                </div>
            </div>

            @if(session('success'))
                <div id="settings-success" class="alert-success flex items-center justify-between gap-4">
                    <span><i class="fas fa-check-circle mr-2"></i>{{ session('success') }}</span>
                    <button type="button" data-dismiss="#settings-success" class="text-emerald-700 hover:text-emerald-900">
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

            @php
                $items = [
                    ['key' => 'attendance', 'label' => 'Absensi', 'icon' => 'fa-fingerprint'],
                    ['key' => 'jobs', 'label' => 'Pekerjaan', 'icon' => 'fa-briefcase'],
                    ['key' => 'user', 'label' => 'User', 'icon' => 'fa-users-gear'],
                ];
            @endphp

            <div class="admin-card">
                <div class="admin-card-header flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="admin-card-title">Kategori Pengaturan</h3>
                        <p class="mt-1 text-xs text-slate-500">Pilih kategori lewat dropdown, lalu simpan perubahan.</p>
                    </div>
                    <div class="w-full sm:w-72">
                        <select
                            class="form-control"
                            onchange="window.location.href=this.value"
                            aria-label="Pilih kategori pengaturan"
                        >
                            @foreach($items as $item)
                                <option
                                    value="{{ route('admin.settings', ['tab' => $item['key']]) }}"
                                    @selected($tab === $item['key'])
                                >
                                    {{ $item['label'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <section class="space-y-4">
                @if($tab === 'attendance')
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Setting Absensi</h3>
                            <p class="mt-1 text-xs text-slate-500">Atur lokasi kantor, radius, jam kerja, dan toleransi keterlambatan.</p>
                        </div>

                        <form method="POST" action="{{ route('admin.settings.attendance') }}" class="admin-card-body grid grid-cols-1 gap-4 md:grid-cols-2" data-submit-lock>
                            @csrf
                            <label class="md:col-span-2">
                                <span class="field-label">Nama Kantor</span>
                                <x-text-input name="name" value="{{ old('name', $officeSetting->name ?? 'Kantor') }}" class="form-control" />
                            </label>
                            <label>
                                <span class="field-label">Latitude</span>
                                <x-text-input name="latitude" value="{{ old('latitude', $officeSetting->latitude ?? '') }}" class="form-control" />
                            </label>
                            <label>
                                <span class="field-label">Longitude</span>
                                <x-text-input name="longitude" value="{{ old('longitude', $officeSetting->longitude ?? '') }}" class="form-control" />
                            </label>
                            <label>
                                <span class="field-label">Radius (meter)</span>
                                <x-text-input name="radius" type="number" min="1" value="{{ old('radius', $officeSetting->radius ?? 50) }}" class="form-control" required />
                            </label>
                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3">
                                <input type="checkbox" name="radius_enforced" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600"
                                    @checked(old('radius_enforced', (bool) ($officeSetting->radius_enforced ?? true)))>
                                <span class="text-sm font-semibold text-slate-700">Wajib dalam radius (blokir jika di luar)</span>
                            </label>
                            <label>
                                <span class="field-label">Jam Check-in (HH:MM)</span>
                                <x-text-input name="check_in_time" value="{{ old('check_in_time', $officeSetting->check_in_time ?? '08:00') }}" class="form-control" required />
                            </label>
                            <label>
                                <span class="field-label">Jam Check-out (HH:MM)</span>
                                <x-text-input name="check_out_time" value="{{ old('check_out_time', $officeSetting->check_out_time ?? '17:00') }}" class="form-control" required />
                            </label>
                            <label>
                                <span class="field-label">Toleransi Telat (menit)</span>
                                <x-text-input name="late_tolerance" type="number" min="0" max="240" value="{{ old('late_tolerance', $officeSetting->late_tolerance ?? 15) }}" class="form-control" required />
                            </label>

                            <div class="md:col-span-2 flex justify-end gap-2 pt-2">
                                <button type="submit" class="btn-primary-soft">Simpan Setting Absensi</button>
                            </div>
                        </form>
                    </div>
                @elseif($tab === 'user')
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Setting User</h3>
                            <p class="mt-1 text-xs text-slate-500">Default akun baru dan kebijakan lokasi.</p>
                        </div>

                        @php
                            $userDefaults = (array) ($userSetting->value ?? []);
                        @endphp

                        <form method="POST" action="{{ route('admin.settings.user') }}" class="admin-card-body grid grid-cols-1 gap-4 md:grid-cols-2" data-submit-lock>
                            @csrf
                            <label class="md:col-span-2">
                                <span class="field-label">Default Password Akun Baru</span>
                                <x-text-input name="default_password" value="{{ old('default_password', $userDefaults['default_password'] ?? 'jonusa123') }}" class="form-control" required />
                                <p class="mt-1 text-xs text-slate-500">Disarankan minimal 8 karakter dan tidak dibagikan sembarangan.</p>
                            </label>

                            <label class="flex items-center gap-3 rounded-xl border border-slate-200 bg-slate-50 px-4 py-3 md:col-span-2">
                                <input type="checkbox" name="requires_location" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600"
                                    @checked(old('requires_location', (bool) ($userDefaults['requires_location'] ?? true)))>
                                <span class="text-sm font-semibold text-slate-700">Akun baru wajib kirim lokasi (requires_location)</span>
                            </label>

                            <label>
                                <span class="field-label">Default Radius User (meter)</span>
                                <x-text-input name="radius_meters" type="number" min="10" max="10000" value="{{ old('radius_meters', $userDefaults['radius_meters'] ?? 100) }}" class="form-control" required />
                            </label>

                            <div class="md:col-span-2 flex justify-end gap-2 pt-2">
                                <button type="submit" class="btn-primary-soft">Simpan Setting User</button>
                            </div>
                        </form>
                    </div>
                @elseif($tab === 'jobs')
                    <div class="admin-card">
                        <div class="admin-card-header">
                            <h3 class="admin-card-title">Setting Pekerjaan</h3>
                            <p class="mt-1 text-xs text-slate-500">Atur label step dan requirement bukti per divisi.</p>
                        </div>

                        <form method="POST" action="{{ route('admin.settings.jobs') }}" class="admin-card-body space-y-4" data-submit-lock>
                            @csrf

                            @php
                                $selectedDivisionId = (int) old('division_id', $divisions->first()?->id);
                                $selectedDivision = $divisions->firstWhere('id', $selectedDivisionId) ?? $divisions->first();
                            @endphp

                            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                                <label class="md:col-span-2">
                                    <span class="field-label">Divisi</span>
                                    <select name="division_id" class="form-control" required>
                                        @foreach($divisions as $division)
                                            <option value="{{ $division->id }}" @selected((int) old('division_id', $selectedDivisionId) === (int) $division->id)>
                                                {{ $division->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <p class="mt-1 text-xs text-slate-500">Pilih divisi yang mau diatur, lalu simpan.</p>
                                </label>

                                @for($i = 1; $i <= 4; $i++)
                                    <label>
                                        <span class="field-label">Nama Step {{ $i }}</span>
                                        <x-text-input name="step_{{ $i }}" value="{{ old('step_'.$i, $selectedDivision?->{'step_'.$i} ?? '') }}" class="form-control" required />
                                    </label>
                                @endfor
                            </div>

                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4">
                                <p class="text-sm font-bold text-slate-800">Requirement Bukti per Step</p>
                                <div class="mt-3 grid grid-cols-1 gap-3 md:grid-cols-3">
                                    @for($i = 1; $i <= 4; $i++)
                                        <div class="rounded-xl border border-slate-200 bg-white p-4">
                                            <p class="text-sm font-semibold text-slate-800">Step {{ $i }}</p>
                                            <div class="mt-3 space-y-2 text-sm">
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="req_desc_{{ $i }}" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600"
                                                        @checked(old('req_desc_'.$i, (bool) ($selectedDivision?->{'req_desc_'.$i} ?? false)))>
                                                    <span>Wajib deskripsi</span>
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="req_photo_{{ $i }}" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600"
                                                        @checked(old('req_photo_'.$i, (bool) ($selectedDivision?->{'req_photo_'.$i} ?? false)))>
                                                    <span>Wajib foto</span>
                                                </label>
                                                <label class="flex items-center gap-2">
                                                    <input type="checkbox" name="req_video_{{ $i }}" value="1" class="h-4 w-4 rounded border-slate-300 text-emerald-600"
                                                        @checked(old('req_video_'.$i, (bool) ($selectedDivision?->{'req_video_'.$i} ?? false)))>
                                                    <span>Wajib video</span>
                                                </label>
                                            </div>
                                        </div>
                                    @endfor
                                </div>
                            </div>

                            <div class="flex justify-end gap-2 pt-2">
                                <button type="submit" class="btn-primary-soft">Simpan Setting Pekerjaan</button>
                            </div>
                        </form>

                        <div class="admin-card-body pt-0">
                            <p class="text-xs text-slate-500">
                                Catatan: setelah memilih divisi lain, halaman perlu reload (via dropdown kategori) agar field mengikuti divisi yang dipilih.
                            </p>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    </div>
</x-app-layout>
