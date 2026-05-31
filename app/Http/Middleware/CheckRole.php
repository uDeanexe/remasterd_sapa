<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    private function normalizeRole(string $role): string
    {
        $role = strtolower(trim($role));

        return match ($role) {
            'admin' => 'administrator',
            'administrator' => 'administrator',
            'kepala' => 'administrator',
            'technician' => 'teknisi',
            'karyawan' => 'teknisi',
            default => $role,
        };
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Cek apakah role user sesuai dengan yang diminta di route.
        // Support multiple roles: "role:kepala,admin" or "role:kepala|admin"
        $allowed = preg_split('/[\\s,|]+/', $role, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $allowed = array_values(array_unique(array_map(fn ($r) => $this->normalizeRole((string) $r), $allowed)));

        $userRole = $this->normalizeRole((string) (Auth::user()->role ?? ''));

        // Administrator has full access across all role-gated routes.
        if ($userRole === 'administrator') {
            return $next($request);
        }

        if (! in_array($userRole, $allowed, true)) {
            // Jika bukan kepala, lempar ke dashboard masing-masing atau beri error 403
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
