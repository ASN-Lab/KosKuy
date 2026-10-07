@extends('layouts.guest')

@section('title', 'Masuk ke KosKuy')
@section('meta_description', 'Masuk ke akun KosKuy untuk mengelola listing kos dan barang thrift kamu.')

@section('form')

    <div class="text-center">
        <span class="mx-auto grid size-14 place-items-center rounded-2xl bg-brand-50 text-brand-600">
            <x-ui.icon name="lock-closed" class="size-6" />
        </span>
        <h1 class="mt-5 text-3xl font-extrabold tracking-tight text-ink-900">Masuk ke KosKuy</h1>
        <p class="mt-2 text-sm text-ink-500">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-bold text-brand-700 hover:underline">Daftar gratis</a>
        </p>
    </div>

    <form method="POST" action="{{ route('login.store') }}" class="mt-8 space-y-5">
        @csrf

        <div>
            <label for="email" class="field-label">Email</label>
            <div class="relative">
                <x-ui.icon name="envelope" class="field-icon" />
                <input
                    id="email"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                    autocomplete="email"
                    placeholder="nama@kampus.ac.id"
                    @error('email') aria-invalid="true" @enderror
                    class="field field-with-icon @error('email') !border-rose-400 !ring-rose-500/15 @enderror"
                >
            </div>
            @error('email')
                <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <div x-data="{ show: false }">
            <div class="flex items-center justify-between">
                <label for="password" class="field-label">Password</label>
            </div>

            <div class="relative">
                <x-ui.icon name="key" class="field-icon" />
                <input
                    id="password"
                    :type="show ? 'text' : 'password'"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Minimal 8 karakter"
                    @error('password') aria-invalid="true" @enderror
                    class="field field-with-icon pr-12 @error('password') !border-rose-400 !ring-rose-500/15 @enderror"
                >
                <button
                    type="button"
                    @click="show = !show"
                    class="absolute top-1/2 right-3 -translate-y-1/2 rounded-lg p-1.5 text-ink-400 transition hover:bg-ink-100 hover:text-ink-700"
                    :aria-label="show ? 'Sembunyikan password' : 'Tampilkan password'"
                >
                    <x-ui.icon name="eye" class="size-4.5" x-show="!show" />
                    <x-ui.icon name="eye-slash" class="size-4.5" x-cloak x-show="show" />
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-[13px] font-semibold text-rose-600">{{ $message }}</p>
            @enderror
        </div>

        <label class="flex cursor-pointer items-center gap-2.5">
            <input
                type="checkbox"
                name="remember"
                value="1"
                @checked(old('remember'))
                class="checkbox"
            >
            <span class="text-sm font-medium text-ink-600">Ingat saya di perangkat ini</span>
        </label>

        <button type="submit" class="btn btn-lg btn-primary w-full">
            <x-ui.icon name="arrow-right" class="size-5" />
            Masuk
        </button>
    </form>

    <div class="mt-8 rounded-2xl border border-brand-100 bg-brand-50/60 p-4">
        <p class="flex items-start gap-2.5 text-[13px] leading-relaxed text-brand-800">
            <x-ui.icon name="information-circle" class="mt-0.5 size-4 shrink-0" />
            <span>
                Lupa password? Fitur reset password akan segera hadir. Sementara ini, hubungi
                <a href="mailto:{{ config('koskuy.contact.email') }}" class="font-bold underline">{{ config('koskuy.contact.email') }}</a>.
            </span>
        </p>
    </div>

@endsection
