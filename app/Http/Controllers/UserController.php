<?php

namespace App\Http\Controllers;

use App\Mail\UserCreatedMail;
use App\Models\User;
use App\Models\Division;
use App\Models\UserRole;
use App\Models\AppSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class UserController extends Controller
{
    private function allowedRoles(): array
    {
        $creatorRole = strtolower((string) (auth()->user()->role ?? ''));
        $creatorRole = match ($creatorRole) {
            'admin', 'kepala' => 'administrator',
            default => $creatorRole,
        };

        if ($creatorRole === 'administrator') {
            return ['administrator', 'cs', 'teknisi', 'hr', 'finance', 'content_creator', 'it_sd'];
        }

        if ($creatorRole === 'hr') {
            // HR tidak boleh membuat HR sendiri.
            return ['cs', 'teknisi', 'finance', 'content_creator', 'it_sd'];
        }

        return [];
    }

    private function divisionNameForRole(string $role): string
    {
        return match ($role) {
            'administrator' => 'Administrator',
            'cs' => 'CS',
            'hr' => 'HR',
            'finance' => 'Finance',
            'content_creator' => 'Content Creator',
            'it_sd' => 'IT / SD',
            default => 'Teknisi',
        };
    }

    public function index(Request $request)
    {
        $users = User::with('division')
            ->orderBy('name', 'asc')
            ->get();

        if ($request->expectsJson()) {
            return response()->json($users);
        }

        $allowedRoles = $this->allowedRoles();
        $roles = empty($allowedRoles)
            ? collect()
            : UserRole::query()
                ->whereIn('role_id', $allowedRoles)
                ->orderBy('role_name')
                ->get();

        $defaults = AppSetting::query()->where('key', 'user_defaults')->value('value') ?? [];
        $defaultPassword = is_array($defaults) ? (string) ($defaults['default_password'] ?? 'jonusa123') : 'jonusa123';

        return view('user.index', compact('users', 'roles', 'defaultPassword'));
    }

    public function store(Request $request)
    {
        $allowedRoles = $this->allowedRoles();
        abort_unless(! empty($allowedRoles), 403);

        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users',
            'role'        => 'required|string|in:'.implode(',', $allowedRoles),
        ]);

        $defaults = AppSetting::query()->where('key', 'user_defaults')->value('value') ?? [];
        $defaultPassword = is_array($defaults) ? (string) ($defaults['default_password'] ?? 'jonusa123') : 'jonusa123';
        $requiresLocation = is_array($defaults) ? (bool) ($defaults['requires_location'] ?? true) : true;
        $radiusMeters = is_array($defaults) ? (int) ($defaults['radius_meters'] ?? 100) : 100;

        $divisionName = $this->divisionNameForRole($request->role);
        $divisionId = Division::query()->where('name', $divisionName)->value('id');
        if (!$divisionId) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['role' => "Divisi untuk role {$request->role} belum tersedia. Jalankan seeder untuk membuat divisi fixed."]);
        }

        $user = User::create([
            'name'                => $request->name,
            'email'               => $request->email,
            'password'            => Hash::make($defaultPassword),
            'division_id'         => $divisionId,
            'role'                => $request->role,
            'is_default_password' => true,
            'requires_location'   => $requiresLocation,
            'radius_meters'       => $radiusMeters,
        ]);

        try {
            $adminEmails = User::query()
                ->where('role', 'administrator')
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->all();

            if (! empty($adminEmails)) {
                Mail::to($adminEmails)->send(new UserCreatedMail($user, $defaultPassword));
            }
        } catch (Throwable $e) {
            Log::warning('Gagal mengirim email akun karyawan baru.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->with('success', "Karyawan berhasil didaftarkan! Password default: {$defaultPassword}")
                ->with('error', 'Akun berhasil dibuat, tetapi email pemberitahuan ke admin gagal dikirim. Periksa konfigurasi email server.');
        }

        return redirect()->back()->with('success', "Karyawan berhasil didaftarkan! Email pemberitahuan dikirim ke admin. Password default: {$defaultPassword}");
    }

    public function resetPassword($id)
    {
        $user = User::findOrFail($id);
        $user->password            = Hash::make('jonusa123');
        $user->is_default_password = true;
        $user->save();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Password {$user->name} berhasil direset ke jonusa123.",
            ]);
        }

        return redirect()->back()->with('success', "Password {$user->name} berhasil direset ke jonusa123.");
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);

            if ($user->id === auth()->id()) {
                return redirect()->back()->with('error', 'Anda tidak bisa menghapus akun sendiri!');
            }

            if (strtolower((string) ($user->role ?? '')) === 'administrator') {
                return redirect()->back()->with('error', 'Akun administrator tidak bisa dihapus.');
            }

            $user->delete();
            return redirect()->back()->with('success', 'Data karyawan berhasil dihapus dari sistem.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
