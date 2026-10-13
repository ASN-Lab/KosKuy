@extends('layouts.app')

@section('title', 'Pengaturan Akun | KosKuy')
@section('meta_description', 'Kelola profil, keamanan, foto, dan preferensi notifikasi akun KosKuy-mu.')

@section('content')

    @php $user = auth()->user(); @endphp

    {{-- ========================= HEADER ========================= --}}
    <section class="relative overflow-hidden border-b border-ink-100 bg-ink-950">
        <div class="grid-lines pointer-events-none absolute inset-0" aria-hidden="true"></div>
        <div class="blob -top-24 -right-16 size-96 bg-brand-500/35 blur-3xl" aria-hidden="true"></div>

        <div class="container-page relative py-10 sm:py-12">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <x-ui.avatar :user="$user" size="size-16" text="text-2xl" class="font-display font-extrabold text-white ring-white/15" />
                    <div>
                        <span class="badge-glass inline-flex">
                            <x-ui.icon name="cog-6-tooth" class="size-3.5 text-accent-300" />
                            {{ $user->roleLabel() }}
                        </span>
                        <h1 class="mt-2.5 text-2xl font-extrabold text-white sm:text-3xl">Pengaturan akun</h1>
                        <p class="mt-1 text-sm text-ink-400">{{ $user->name }} · {{ $user->email }}</p>
                    </div>
                </div>

                <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                    <a href="{{ route('dashboard') }}" class="btn btn-md w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto">
                        <x-ui.icon name="chevron-left" class="size-4" />
                        <span>Dashboard</span>
                    </a>
                    <x-logout-button class="btn btn-md btn-white w-full sm:w-auto" />
                </div>
            </div>
        </div>
    </section>

    {{-- ========================= BODY ========================= --}}
    <section class="section-tight">
        <div class="container-page">
            <div class="grid gap-8 lg:grid-cols-[220px_1fr] lg:gap-10">

                {{-- Section nav --}}
                <nav aria-label="Bagian pengaturan" class="lg:sticky lg:top-24 lg:self-start">
                    <ul class="flex gap-2 overflow-x-auto pb-1 lg:flex-col lg:gap-1 lg:overflow-visible lg:pb-0">
                        @foreach ([
                            ['id' => 'profil', 'label' => 'Profil', 'icon' => 'user-circle'],
                            ['id' => 'foto', 'label' => 'Foto profil', 'icon' => 'photo'],
                            ['id' => 'keamanan', 'label' => 'Keamanan', 'icon' => 'key'],
                            ['id' => 'notifikasi', 'label' => 'Notifikasi', 'icon' => 'bell'],
                            ['id' => 'hapus', 'label' => 'Hapus akun', 'icon' => 'trash'],
                        ] as $tab)
                            <li class="shrink-0">
                                <a href="#{{ $tab['id'] }}" class="flex items-center gap-2.5 rounded-2xl px-3.5 py-2.5 text-sm font-semibold whitespace-nowrap text-ink-600 transition hover:bg-ink-100 hover:text-ink-900 lg:whitespace-normal">
                                    <x-ui.icon :name="$tab['icon']" class="size-4 text-ink-400" />
                                    {{ $tab['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="space-y-6">

                    {{-- Profil --}}
                    <div id="profil" class="card scroll-mt-24 p-6 sm:p-8">
                        <h2 class="text-lg font-extrabold">Profil</h2>
                        <p class="mt-1 text-sm text-ink-500">Nama dan kontak ini tampil saat kamu berinteraksi lewat KosKuy.</p>

                        <form method="POST" action="{{ route('settings.profile') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                            @csrf
                            @method('PUT')

                            <div class="sm:col-span-2">
                                <label for="name" class="field-label">Nama lengkap</label>
                                <div class="relative">
                                    <x-ui.icon name="user-circle" class="field-icon" />
                                    <input id="name" type="text" name="name" value="{{ old('name', $user->name) }}" required
                                        class="field field-with-icon @error('name') !border-rose-400 !ring-rose-500/15 @enderror">
                                </div>
                                @error('name') <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="email" class="field-label">Email</label>
                                <div class="relative">
                                    <x-ui.icon name="envelope" class="field-icon" />
                                    <input id="email" type="email" name="email" value="{{ old('email', $user->email) }}" required
                                        class="field field-with-icon @error('email') !border-rose-400 !ring-rose-500/15 @enderror">
                                </div>
                                @error('email') <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="phone" class="field-label">Nomor WhatsApp</label>
                                <div class="relative">
                                    <x-ui.icon name="chat-bubble-oval-left" class="field-icon" />
                                    <input id="phone" type="text" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="08xxxxxxxxxx"
                                        class="field field-with-icon @error('phone') !border-rose-400 !ring-rose-500/15 @enderror">
                                </div>
                                @error('phone') <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="sm:col-span-2">
                                <button type="submit" class="btn btn-md btn-primary w-full sm:w-auto">
                                    <x-ui.icon name="check" class="size-4" />
                                    Simpan profil
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Foto --}}
                    <div id="foto" class="card scroll-mt-24 p-6 sm:p-8">
                        <h2 class="text-lg font-extrabold">Foto profil</h2>
                        <p class="mt-1 text-sm text-ink-500">Format JPG, PNG, atau WEBP. Maksimal 2 MB.</p>

                        <div class="mt-6 flex flex-col gap-5 sm:flex-row sm:items-center">
                            <x-ui.avatar :user="$user" size="size-24" text="text-3xl" class="font-display font-extrabold" />

                            <form method="POST" action="{{ route('settings.avatar') }}" enctype="multipart/form-data" class="flex-1 space-y-3">
                                @csrf

                                <input type="file" name="avatar" accept="image/png,image/jpeg,image/webp" required
                                    class="block w-full cursor-pointer rounded-2xl border border-ink-200 bg-white px-3.5 py-2.5 text-sm text-ink-600 file:mr-3 file:rounded-full file:border-0 file:bg-brand-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100 @error('avatar') !border-rose-400 @enderror">
                                @error('avatar') <p class="text-[13px] font-semibold text-rose-600">{{ $message }}</p> @enderror

                                <div class="flex flex-col gap-2 sm:flex-row">
                                    <button type="submit" class="btn btn-md btn-primary w-full sm:w-auto">
                                        <x-ui.icon name="arrow-up-tray" class="size-4" />
                                        Unggah foto
                                    </button>
                                </div>
                            </form>
                        </div>

                        @if ($user->avatar_path)
                            <form method="POST" action="{{ route('settings.avatar.destroy') }}" class="mt-4">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline text-rose-600 hover:border-rose-300 hover:bg-rose-50 hover:text-rose-700">
                                    <x-ui.icon name="trash" class="size-4" />
                                    Hapus foto saat ini
                                </button>
                            </form>
                        @endif
                    </div>

                    {{-- Keamanan --}}
                    <div id="keamanan" class="card scroll-mt-24 p-6 sm:p-8">
                        <h2 class="text-lg font-extrabold">Keamanan</h2>
                        <p class="mt-1 text-sm text-ink-500">Ganti password secara berkala agar akunmu tetap aman.</p>

                        <form method="POST" action="{{ route('settings.password') }}" class="mt-6 grid gap-5 sm:grid-cols-2">
                            @csrf
                            @method('PUT')

                            <div class="sm:col-span-2">
                                <label for="current_password" class="field-label">Password saat ini</label>
                                <div class="relative" x-data="{ show: false }">
                                    <x-ui.icon name="lock-closed" class="field-icon" />
                                    <input id="current_password" name="current_password" required autocomplete="current-password"
                                        :type="show ? 'text' : 'password'"
                                        class="field field-with-icon field-with-password-toggle @error('current_password') !border-rose-400 !ring-rose-500/15 @enderror">
                                    <button type="button" class="field-eye-toggle" @click="show = !show"
                                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                        <x-ui.icon name="eye" class="size-4" x-show="!show" />
                                        <x-ui.icon name="eye-slash" class="size-4" x-cloak x-show="show" />
                                    </button>
                                </div>
                                @error('current_password') <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password" class="field-label">Password baru</label>
                                <div class="relative" x-data="{ show: false }">
                                    <input id="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter"
                                        :type="show ? 'text' : 'password'"
                                        class="field field-with-password-toggle @error('password') !border-rose-400 !ring-rose-500/15 @enderror">
                                    <button type="button" class="field-eye-toggle" @click="show = !show"
                                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                        <x-ui.icon name="eye" class="size-4" x-show="!show" />
                                        <x-ui.icon name="eye-slash" class="size-4" x-cloak x-show="show" />
                                    </button>
                                </div>
                                @error('password') <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="field-label">Konfirmasi password baru</label>
                                <div class="relative" x-data="{ show: false }">
                                    <input id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                                        :type="show ? 'text' : 'password'"
                                        class="field field-with-password-toggle">
                                    <button type="button" class="field-eye-toggle" @click="show = !show"
                                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                        <x-ui.icon name="eye" class="size-4" x-show="!show" />
                                        <x-ui.icon name="eye-slash" class="size-4" x-cloak x-show="show" />
                                    </button>
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <button type="submit" class="btn btn-md btn-primary w-full sm:w-auto">
                                    <x-ui.icon name="shield-check" class="size-4" />
                                    Ganti password
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Notifikasi --}}
                    <div id="notifikasi" class="card scroll-mt-24 p-6 sm:p-8">
                        <h2 class="text-lg font-extrabold">Notifikasi</h2>
                        <p class="mt-1 text-sm text-ink-500">Atur bagaimana kami menghubungimu soal listing dan akun.</p>

                        <form method="POST" action="{{ route('settings.notifications') }}" class="mt-6 space-y-3">
                            @csrf
                            @method('PUT')

                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-ink-100 p-4 transition hover:bg-ink-50">
                                <input type="checkbox" name="notify_email" value="1" class="checkbox mt-0.5" @checked(old('notify_email', $user->notify_email))>
                                <span>
                                    <span class="block text-sm font-bold text-ink-900">Email</span>
                                    <span class="mt-0.5 block text-[13px] text-ink-500">Pengingat tagihan, pesan baru, dan pembaruan listing.</span>
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-start gap-3 rounded-2xl border border-ink-100 p-4 transition hover:bg-ink-50">
                                <input type="checkbox" name="notify_whatsapp" value="1" class="checkbox mt-0.5" @checked(old('notify_whatsapp', $user->notify_whatsapp))>
                                <span>
                                    <span class="block text-sm font-bold text-ink-900">WhatsApp</span>
                                    <span class="mt-0.5 block text-[13px] text-ink-500">Notifikasi penting langsung ke nomor WhatsApp-mu.</span>
                                </span>
                            </label>

                            <button type="submit" class="btn btn-md btn-primary w-full sm:w-auto">
                                <x-ui.icon name="bell" class="size-4" />
                                Simpan preferensi
                            </button>
                        </form>
                    </div>

                    {{-- Hapus akun --}}
                    <div id="hapus" class="card scroll-mt-24 !border-rose-200 p-6 sm:p-8" x-data="{ confirm: false }">
                        <h2 class="text-lg font-extrabold text-rose-700">Hapus akun</h2>
                        <p class="mt-1 text-sm text-ink-500">
                            Menghapus akun bersifat permanen. Semua listing kos dan barang thrift milikmu ikut terhapus dan tidak bisa dikembalikan.
                        </p>

                        <button type="button" @click="confirm = true" x-show="!confirm" class="btn btn-md mt-6 w-full border border-rose-200 bg-white text-rose-700 hover:bg-rose-50 sm:w-auto">
                            <x-ui.icon name="trash" class="size-4" />
                            Hapus akun saya
                        </button>

                        <form x-show="confirm" x-cloak method="POST" action="{{ route('settings.destroy') }}" class="mt-6 max-w-md space-y-4 rounded-2xl border border-rose-200 bg-rose-50 p-4">
                            @csrf
                            @method('DELETE')

                            <div>
                                <label for="delete_password" class="field-label">Masukkan password untuk konfirmasi</label>
                                <div class="relative" x-data="{ show: false }">
                                    <x-ui.icon name="lock-closed" class="field-icon" />
                                    <input id="delete_password" name="password" required autocomplete="current-password"
                                        :type="show ? 'text' : 'password'"
                                        class="field field-with-icon field-with-password-toggle @error('password') !border-rose-400 !ring-rose-500/15 @enderror">
                                    <button type="button" class="field-eye-toggle" @click="show = !show"
                                        :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'">
                                        <x-ui.icon name="eye" class="size-4" x-show="!show" />
                                        <x-ui.icon name="eye-slash" class="size-4" x-cloak x-show="show" />
                                    </button>
                                </div>
                                @error('password') <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2 sm:flex-row">
                                <button type="submit" class="btn btn-md w-full bg-rose-600 text-white hover:bg-rose-700 active:translate-y-px sm:w-auto">
                                    <x-ui.icon name="trash" class="size-4" />
                                    Ya, hapus permanen
                                </button>
                                <button type="button" @click="confirm = false" class="btn btn-md btn-outline w-full sm:w-auto">Batal</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </section>

@endsection
