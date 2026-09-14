<?php

namespace App\Http\Controllers;

use App\Models\Aplicativo;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $organizationId = $request->user()->organization_id;
        $users = User::query()->where('organization_id', $organizationId);
        $totalUsers = (clone $users)->count();
        $googleUsers = (clone $users)->whereNotNull('google_id')->count();
        $aplicativos = Aplicativo::query()
            ->where('organization_id', $organizationId)
            ->with('modulos')
            ->orderBy('nombre_aplicativo')
            ->get();

        return view('dashboard', [
            'totalUsers' => $totalUsers,
            'totalApplications' => $aplicativos->count(),
            'totalModules' => $aplicativos->sum(fn (Aplicativo $aplicativo): int => $aplicativo->modulos->count()),
            'newUsersThisMonth' => (clone $users)->where('created_at', '>=', now()->startOfMonth())->count(),
            'googleUsers' => $googleUsers,
            'googleUsersPercentage' => $totalUsers === 0 ? 0 : (int) round($googleUsers / $totalUsers * 100),
            'recentUsers' => $users->latest()->limit(5)->get(),
            'aplicativos' => $aplicativos,
        ]);
    }
}
