@props([
    'label' => 'Keluar',
    'icon' => 'arrow-path',
    'class' => 'btn btn-md btn-outline',
])

{{--
    Logout trigger + confirmation dialog. Self-contained Alpine state, with the
    dialog teleported to <body> so it is never clipped by a sticky/fixed parent.
--}}
<div x-data="{ confirmLogout: false, submitting: false }" class="contents" @pageshow.window="submitting = false">
    <button type="button" @click="confirmLogout = true" {{ $attributes->merge(['class' => $class]) }}>
        <x-ui.icon :name="$icon" class="size-4" />
        {{ $label }}
    </button>

    <template x-teleport="body">
        <div
            x-show="confirmLogout"
            x-cloak
            x-transition.opacity
            @keydown.escape.window="confirmLogout = false"
            class="fixed inset-0 z-[100] flex items-end justify-center bg-ink-950/60 p-4 backdrop-blur-sm sm:items-center"
            role="dialog"
            aria-modal="true"
            aria-labelledby="logout-dialog-title"
        >
            <div
                x-show="confirmLogout"
                x-transition
                @click.outside="confirmLogout = false"
                class="w-full max-w-sm rounded-3xl bg-white p-6 shadow-lift"
            >
                <span class="grid size-12 place-items-center rounded-2xl bg-red-50 text-red-600">
                    <x-ui.icon name="arrow-right-start-on-rectangle" class="size-6" />
                </span>

                <h2 id="logout-dialog-title" class="mt-4 text-lg font-extrabold text-ink-900">
                    Keluar dari akun?
                </h2>
                <p class="mt-1.5 text-sm leading-relaxed text-ink-500">
                    Kamu akan keluar dari sesi ini. Bisa masuk lagi kapan saja dengan email dan password-mu.
                </p>

                <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end">
                    <button type="button" @click="confirmLogout = false" class="btn btn-md btn-outline w-full sm:w-auto">
                        Batal
                    </button>
                    <form
                        method="POST"
                        action="{{ route('logout') }}"
                        class="w-full sm:w-auto"
                        @submit="if (submitting) { $event.preventDefault(); } else { submitting = true; }"
                    >
                        @csrf
                        <button type="submit" :disabled="submitting" class="btn btn-md w-full bg-red-600 text-white hover:bg-red-700 active:translate-y-px disabled:cursor-wait disabled:opacity-70 sm:w-auto">
                            <x-ui.icon name="arrow-right-start-on-rectangle" class="size-4" />
                            Ya, keluar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </template>
</div>
