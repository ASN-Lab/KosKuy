<?php

namespace App\Http\Controllers;

use App\Models\KosListing;
use App\Models\ThriftProduct;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Email atau password tidak cocok dengan data kami.']);
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'string', 'max:25', 'regex:/^[0-9+\-\s()]+$/'],
            'role' => ['required', 'in:student,owner'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'terms' => ['accepted'],
        ], [
            'terms.accepted' => 'Kamu harus menyetujui syarat dan ketentuan terlebih dahulu.',
            'phone.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, dan tanda +.',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'role' => $data['role'],
            'password' => $data['password'],
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Selamat bergabung, ' . $user->name . '! Akunmu sudah siap dipakai.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Kamu sudah keluar. Sampai jumpa lagi.');
    }

    public function dashboard(): View
    {
        $user = Auth::user();

        $kosListings = KosListing::where('owner_id', $user->id)
            ->with(['images', 'facilities'])
            ->latest()
            ->get();

        $thriftProducts = ThriftProduct::where('seller_id', $user->id)
            ->with(['images', 'category'])
            ->latest()
            ->get();

        $stats = [
            'kos' => $kosListings->count(),
            'kos_available' => $kosListings->where('is_available', true)->count(),
            'thrift' => $thriftProducts->count(),
            'thrift_active' => $thriftProducts->where('is_sold', false)->count(),
        ];

        return view('auth.dashboard', compact('kosListings', 'thriftProducts', 'stats'));
    }
}