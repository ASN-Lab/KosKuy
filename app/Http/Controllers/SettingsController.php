<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function edit(): View
    {
        return view('settings.index', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:25', 'regex:/^[0-9+\-\s()]+$/'],
        ], [
            'phone.regex' => 'Nomor WhatsApp hanya boleh berisi angka, spasi, dan tanda +.',
        ]);

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ], [
            'current_password.current_password' => 'Password saat ini tidak cocok.',
        ]);

        $request->user()->update(['password' => $data['password']]);

        return back()->with('success', 'Password berhasil diganti.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'avatar.max' => 'Ukuran foto maksimal 2 MB.',
            'avatar.image' => 'Berkas harus berupa gambar.',
        ]);

        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar_path' => $path]);

        return back()->with('success', 'Foto profil diperbarui.');
    }

    public function destroyAvatar(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
            $user->update(['avatar_path' => null]);
        }

        return back()->with('success', 'Foto profil dihapus.');
    }

    public function updateNotifications(Request $request): RedirectResponse
    {
        $request->user()->update([
            'notify_email' => $request->boolean('notify_email'),
            'notify_whatsapp' => $request->boolean('notify_whatsapp'),
        ]);

        return back()->with('success', 'Preferensi notifikasi disimpan.');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'Password tidak cocok. Akun tidak dihapus.',
        ]);

        $user = $request->user();

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($user->avatar_path) {
            Storage::disk('public')->delete($user->avatar_path);
        }

        $user->delete();

        return redirect()->route('home')->with('success', 'Akunmu sudah dihapus. Terima kasih sudah memakai KosKuy.');
    }
}
