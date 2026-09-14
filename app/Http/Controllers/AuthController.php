<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    public function create(): Response
    {
        return response()->view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['email' => 'Las credenciales no son válidas.'])->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route(
            $request->user()->is_organization_admin ? 'dashboard' : 'member.home'
        ));
    }

    public function showRegistration(): Response
    {
        return response()->view('auth.register', [
            'organizations' => Organization::query()->orderBy('name')->get(),
        ]);
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', 'min:12'],
            'organization_id' => ['nullable', 'integer', 'exists:organizations,id', 'required_without:organization_name'],
            'organization_name' => ['nullable', 'string', 'max:255', 'required_without:organization_id'],
        ]);

        $user = DB::transaction(function () use ($data): User {
            $organization = filled($data['organization_id'] ?? null)
                ? Organization::findOrFail($data['organization_id'])
                : Organization::firstOrCreate(['name' => trim($data['organization_name'])]);

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => $data['password'],
                'organization_id' => $organization->id,
                'is_organization_admin' => $organization->wasRecentlyCreated,
            ]);
        });
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route($user->is_organization_admin ? 'dashboard' : 'member.home');
    }

    public function redirectToGoogle(): Response|RedirectResponse
    {
        if (! config('services.google.client_id') || ! config('services.google.client_secret')) {
            return redirect()->route('login')->withErrors([
                'google' => 'El acceso con Google aún no está configurado. Define las credenciales OAuth en el entorno seguro.',
            ]);
        }

        return Socialite::driver('google')->scopes(['openid', 'profile', 'email'])->redirect();
    }

    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Throwable) {
            return redirect()->route('login')->withErrors(['google' => 'No fue posible completar el acceso con Google. Inténtalo de nuevo.']);
        }

        if (! $googleUser->getEmail()) {
            return redirect()->route('login')->withErrors(['google' => 'Google no proporcionó una dirección de correo para esta cuenta.']);
        }

        if (! filter_var($googleUser->user['email_verified'] ?? false, FILTER_VALIDATE_BOOLEAN)) {
            return redirect()->route('login')->withErrors(['google' => 'Google no confirmó que el correo de esta cuenta esté verificado.']);
        }

        $user = User::where('google_id', $googleUser->getId())->first()
            ?? User::where('email', $googleUser->getEmail())->first();

        if (! $user) {
            $user = User::create([
                'name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'Usuario SIGA',
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'google_id' => $googleUser->getId(),
                'password' => Hash::make(Str::random(64)),
            ]);
        } elseif (! $user->google_id) {
            $user->update(['google_id' => $googleUser->getId()]);
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showForgotPassword(): Response
    {
        return response()->view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $status = Password::sendResetLink($request->only('email'));

        return back()->with(
            $status === Password::RESET_LINK_SENT ? 'status' : 'error',
            __($status),
        );
    }

    public function showResetPassword(Request $request, string $token): Response
    {
        return response()->view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:12'],
        ]);

        $status = Password::reset($credentials, function (User $user, string $password): void {
            $user->forceFill([
                'password' => $password,
                'remember_token' => Str::random(60),
            ])->save();
        });

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
