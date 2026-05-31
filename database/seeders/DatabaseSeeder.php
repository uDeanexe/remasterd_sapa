<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Division;
use App\Models\OfficeSetting;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Role master tetap disediakan di DB walaupun user yang aktif hanya administrator.
        $roles = [
            ['role_id' => 'administrator', 'role_name' => 'Administrator', 'description' => 'Akses penuh sistem'],
            ['role_id' => 'cs', 'role_name' => 'Customer Service', 'description' => 'Akses pekerjaan & client'],
            ['role_id' => 'hr', 'role_name' => 'HR', 'description' => 'Akses SDM & absensi (view)'],
            ['role_id' => 'finance', 'role_name' => 'Finance', 'description' => 'Akses keuangan'],
            ['role_id' => 'teknisi', 'role_name' => 'Teknisi', 'description' => 'Akses teknisi/karyawan'],
            ['role_id' => 'content_creator', 'role_name' => 'Content Creator', 'description' => 'Akses catatan kerja konten'],
            ['role_id' => 'it_sd', 'role_name' => 'IT / SD', 'description' => 'Akses catatan kerja IT/SD'],
        ];

        foreach ($roles as $row) {
            UserRole::query()->updateOrCreate(
                ['role_id' => $row['role_id']],
                [
                    'role_name' => $row['role_name'],
                    'description' => $row['description'] ?? null,
                    'transaction_date' => now()->toDateString(),
                ]
            );
        }

        $divisionNamesByRole = [
            'administrator' => 'Administrator',
            'kepala' => 'Kepala',
            'cs' => 'CS',
            'hr' => 'HR',
            'finance' => 'Finance',
            'teknisi' => 'Teknisi',
            'content_creator' => 'Content Creator',
            'it_sd' => 'IT / SD',
        ];

        $divisionDefaultsByName = [
            'Administrator' => [
                'step_1' => 'Setup Sistem',
                'step_2' => 'Kelola Akun & Role',
                'step_3' => 'Audit & Monitoring',
                'step_4' => 'Support & Closing',
                'req_desc_1' => true,
                'req_desc_2' => true,
                'req_desc_3' => true,
                'req_desc_4' => true,
            ],
            'Kepala' => [
                'step_1' => 'Review Permintaan',
                'step_2' => 'Approve Jadwal/Biaya',
                'step_3' => 'Monitor Eksekusi',
                'step_4' => 'Evaluasi & Feedback',
                'req_desc_1' => true,
                'req_desc_2' => true,
                'req_desc_3' => true,
                'req_desc_4' => true,
            ],
            'CS' => [
                'step_1' => 'Terima Laporan',
                'step_2' => 'Verifikasi Data',
                'step_3' => 'Jadwalkan Teknisi',
                'step_4' => 'Konfirmasi & Tutup',
                'req_desc_1' => true,
                'req_desc_2' => true,
                'req_desc_3' => false,
                'req_desc_4' => true,
            ],
            'HR' => [
                'step_1' => 'Administrasi SDM',
                'step_2' => 'Onboarding',
                'step_3' => 'Absensi & Perizinan',
                'step_4' => 'Evaluasi Kinerja',
                'req_desc_1' => true,
                'req_desc_2' => true,
                'req_desc_3' => true,
                'req_desc_4' => true,
            ],
            'Finance' => [
                'step_1' => 'Buat Tagihan',
                'step_2' => 'Konfirmasi Pembayaran',
                'step_3' => 'Rekonsiliasi',
                'step_4' => 'Laporan',
                'req_desc_1' => true,
                'req_desc_2' => true,
                'req_desc_3' => true,
                'req_desc_4' => true,
            ],
            'Teknisi' => [
                'step_1' => 'Survey & Persiapan',
                'step_2' => 'Instalasi Perangkat',
                'step_3' => 'Konfigurasi & Test',
                'step_4' => 'Dokumentasi & Closing',
                'req_desc_1' => true,
                'req_photo_1' => true,
                'req_desc_2' => true,
                'req_photo_2' => true,
                'req_desc_3' => true,
                'req_desc_4' => true,
                'req_photo_4' => true,
            ],
            'Content Creator' => [
                'step_1' => 'Brief & Riset',
                'step_2' => 'Produksi Konten',
                'step_3' => 'Review & Revisi',
                'step_4' => 'Publish & Report',
                'req_desc_1' => true,
                'req_desc_2' => true,
                'req_desc_3' => true,
                'req_desc_4' => true,
            ],
            'IT / SD' => [
                'step_1' => 'Analisa',
                'step_2' => 'Implementasi',
                'step_3' => 'Testing',
                'step_4' => 'Dokumentasi & Closing',
                'req_desc_1' => true,
                'req_desc_2' => true,
                'req_desc_3' => true,
                'req_desc_4' => true,
            ],
        ];

        $divisionIdsByRole = [];
        foreach ($divisionNamesByRole as $role => $divisionName) {
            $division = Division::query()->updateOrCreate(
                ['name' => $divisionName],
                $divisionDefaultsByName[$divisionName] ?? [
                    'step_1' => 'Persiapan',
                    'step_2' => 'Proses',
                    'step_3' => 'Eksekusi',
                    'step_4' => 'Selesai & Kendala',
                ]
            );

            $divisionIdsByRole[$role] = $division->id;
        }

        OfficeSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'name' => 'Kantor',
                'latitude' => -6.200000,
                'longitude' => 106.816666,
                'radius' => 50,
                'radius_enforced' => true,
                'check_in_time' => '08:00',
                'check_out_time' => '17:00',
                'late_tolerance' => 15,
            ]
        );

        // Default account: hanya administrator. User role lain dibuat manual dari menu admin/HR sesuai kebutuhan.
        $users = [
            ['name' => 'Administrator', 'email' => 'hallo@jonusa.net', 'role' => 'administrator'],
        ];

        foreach ($users as $row) {
            User::query()->updateOrCreate(
                ['email' => $row['email']],
                [
                    'name' => $row['name'],
                    'password' => Hash::make('password123'),
                    'role' => $row['role'],
                    'division_id' => $divisionIdsByRole[$row['role']] ?? $divisionIdsByRole['teknisi'],
                    'is_default_password' => false,
                ]
            );
        }
    }
}
