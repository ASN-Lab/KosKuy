@extends('layouts.guest')

@section('title', 'Daftar Gratis — KosKuy')
@section('meta_description', 'Buat akun KosKuy gratis untuk mengelola listing kos dan barang thrift kamu.')

@section('form')

    <div class="text-center">
        <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-accent-50 text-accent-600">
            <x-ui.icon name="user-plus" class="size-6" />
        </span>
        <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-ink-900">Buat akun gratis</h1>
        <p class="mt-2 text-sm text-ink-500">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-bold text-brand-700 hover:underline">Masuk di sini</a>
        </p>
    </div>

    <form method="POST" action="{{ route('register.store') }}" class="mt-8 space-y-5">
        @csrf

        <fieldset>
            <legend class="field-label">Saya daftar sebagai</legend>

            @php
                $roles = [
                    'student' => [
                        'label' => 'Mahasiswa / Pencari Kos',
                        'description' => 'Cari kos dan belanja thrift dengan gratis.',
                        'icon' => 'academic-cap',
                    ],
                    'owner' => [
                        'label' => 'Pemilik Kos / Penjual',
                        'description' => 'Pasang listing dan kelola barang thrift kamu.',
                        'icon' => 'building-office-2',
                    ],
                ];
            @endphp

            <div class="grid gap-3">
                @foreach ($roles as $value => $role)
                    <label class="relative cursor-pointer">
                        <input
                            type="radio"
                            name="role"
                            value="{{ $value }}"
                            @checked(old('role', 'student') === $value)
                            class="peer sr-only"
                        >
                        <span class="flex items-start gap-3.5 rounded-2xl border border-ink-200 bg-white p-4 transition hover:border-brand-300 hover:bg-brand-50/40 peer-checked:border-brand-500 peer-checked:bg-brand-50 peer-checked:ring-4 peer-checked:ring-brand-500/12">
                            <span class="grid size-10 shrink-0 place-items-center rounded-xl bg-brand-100 text-brand-700 transition peer-checked:bg-brand-600 peer-checked:text-white">
                                <x-ui.icon :name="$role['icon']" class="size-5" />
                            </span>
                            <span class="min-w-0">
                                <span class="block text-sm font-bold text-ink-900">{{ $role['label'] }}</span>
                                <span class="mt-0.5 block text-[13px] leading-relaxed text-ink-500">{{ $role['description'] }}</span>
                            </span>
                        </span>
                    </label>
                @endforeach
            </div>
            @error('role')
                <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </fieldset>

        <div>
            <label for="name" class="field-label">Nama lengkap</label>
            <div class="relative">
                <x-ui.icon name="users" class="field-icon" />
                <input
                    id="name"
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autocomplete="name"
                    placeholder="Nama sesuai identitas"
                    class="field field-with-icon @error('name') !border-rose-400 !ring-rose-500/15 @enderror"
                >
            </div>
            @error('name')
                <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="register-email" class="field-label">Email</label>
            <div class="relative">
                <x-ui.icon name="envelope" class="field-icon" />
                <input
                    id="register-email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autocomplete="email"
                    placeholder="nama@kampus.ac.id"
                    class="field field-with-icon @error('email') !border-rose-400 !ring-rose-500/15 @enderror"
                >
            </div>
            @error('email')
                <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="phone" class="field-label">Nomor WhatsApp</label>
            <div class="relative">
                <x-ui.icon name="chat-bubble-oval-left" class="field-icon" />
                <input
                    id="phone"
                    type="tel"
                    name="phone"
                    value="{{ old('phone') }}"
                    required
                    inputmode="tel"
                    autocomplete="tel"
                    placeholder="08xxxxxxxxxx"
                    class="field field-with-icon @error('phone') !border-rose-400 !ring-rose-500/15 @enderror"
                >
            </div>
            @error('phone')
                <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
            <p class="mt-1.5 text-xs text-ink-400">Nomor ini hanya dilihat pengguna yang tertarik dengan listingmu.</p>
        </div>

        <div x-data="{ show: false }">
            <label for="register-password" class="field-label">Password</label>
            <div class="relative">
                <x-ui.icon name="key" class="field-icon" />
                <input
                    id="register-password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Minimal 8 karakter"
                    class="field field-with-icon pr-12 @error('password') !border-rose-400 !ring-rose-500/15 @enderror"
                >
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute top-1/2 right-3 -translate-y-1/2 rounded-lg p-1.5 text-ink-400 transition hover:bg-ink-100 hover:text-ink-700"
                    :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                    <x-ui.icon name="information-circle" class="size-4.5" x-show="!show" />
                    <x-ui.icon name="minus-circle" class="size-4.5" x-cloak x-show="show" />
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="password_confirmation" class="field-label">Ulangi password</label>
            <div class="relative">
                <x-ui.icon name="shield-check" class="field-icon" />
                <input
                    id="password_confirmation"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ketik ulang password"
                    class="field field-with-icon"
                >
            </div>
        </div>

        <label class="flex cursor-pointer items-start gap-2.5">
            <input
                type="checkbox"
                name="terms"
                value="1"
                @checked(old('terms'))
                required
                class="checkbox mt-0.5"
            >
            <span class="text-sm leading-relaxed text-ink-600">
                Saya menyetujui
                <a href="{{ route('home') }}#legal" class="font-semibold text-brand-700 hover:underline">Syarat &amp; Ketentuan</a>
                serta
                <a href="{{ route('home') }}#legal" class="font-semibold text-brand-700 hover:underline">Kebijakan Privasi</a>
                KosKuy.
            </span>
        </label>
        @error('terms')
            <p class="-mt-3 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
        @enderror

        <button type="submit" class="btn btn-lg btn-primary w-full">
            <x-ui.icon name="rocket-launch" class="size-5" />
            Daftar Sekarang
        </button>

        <p class="text-center text-xs leading-relaxed text-ink-400">
            Dengan mendaftar kamu langsung mendapat akses ke dashboard listing.
            Tidak ada biaya tersembunyi.
        </p>
    </form>

@endsection