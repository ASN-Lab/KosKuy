{{--
    Lost-connection screen. Shown by the `offlineScreen` Alpine component whenever
    the browser reports `offline`, a network request fails, or a navigation is
    attempted with no connectivity. It hides itself again once the connection is
    verified back.
--}}
<div x-data="offlineScreen" x-cloak @keydown.escape.window="dismiss()">

    {{-- ========================= FULL-SCREEN OVERLAY ========================= --}}
    <div
        x-show="(!online && !dismissed) || restored"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        x-effect="if (!online && !dismissed && !restored) $nextTick(() => $refs.retry?.focus())"
        class="fixed inset-0 z-[100] grid place-items-center overflow-hidden bg-ink-950/95 p-5 backdrop-blur-xl"
        role="alertdialog"
        aria-modal="true"
        aria-labelledby="offline-title"
        aria-describedby="offline-desc"
    >
        <div class="grid-lines pointer-events-none absolute inset-0 opacity-60" aria-hidden="true"></div>
        <div class="blob pointer-events-none -top-24 -right-16 size-96 bg-brand-500/30 blur-3xl" aria-hidden="true"></div>
        <div class="blob pointer-events-none -bottom-32 left-10 size-96 bg-accent-400/20 blur-3xl" aria-hidden="true"></div>

        <div class="relative w-full max-w-md text-center">

            {{-- ---------- Restored state ---------- --}}
            <div x-show="restored" x-cloak x-transition.opacity.duration.300ms class="flex flex-col items-center">
                <span class="grid size-20 place-items-center rounded-3xl bg-accent-500/15 text-accent-300 animate-pop">
                    <x-ui.icon name="check-badge" class="size-10" />
                </span>
                <h1 id="offline-title" class="mt-6 text-2xl font-extrabold text-white sm:text-3xl">Sinyal kembali!</h1>
                <p id="offline-desc" class="mt-3 text-sm leading-relaxed text-ink-300">
                    Koneksi internetmu tersambung lagi. Sebentar ya, halaman dilanjutkan otomatis.
                </p>
            </div>

            {{-- ---------- Offline state ---------- --}}
            <div x-show="!restored" class="flex flex-col items-center">
                <span class="badge-glass inline-flex mb-7">
                    <x-ui.icon name="wifi" class="size-3.5 text-rose-300" />
                    Tidak ada koneksi
                </span>

                {{-- Animated scene --}}
                <div class="offline-scene relative grid size-44 place-items-center sm:size-52">
                    {{-- drifting clouds --}}
                    <x-ui.icon name="cloud" class="absolute top-2 left-3 size-10 text-white/15 animate-drift" />
                    <x-ui.icon name="cloud" class="absolute right-2 bottom-6 size-14 text-white/10 animate-drift" style="animation-delay: 1.6s" />

                    {{-- expanding signal rings --}}
                    <span class="absolute inset-2 rounded-full border border-brand-400/40 animate-signal-ring"></span>
                    <span class="absolute inset-2 rounded-full border border-accent-400/40 animate-signal-ring" style="animation-delay: 1.07s"></span>
                    <span class="absolute inset-2 rounded-full border border-brand-400/40 animate-signal-ring" style="animation-delay: 2.14s"></span>

                    {{-- rotating radar sweep --}}
                    <span
                        class="absolute inset-4 rounded-full animate-signal-sweep"
                        style="background: conic-gradient(from 0deg, transparent 0deg, rgb(99 102 241 / 0.35) 65deg, transparent 135deg);"
                        aria-hidden="true"
                    ></span>

                    {{-- core --}}
                    <span class="relative grid size-24 place-items-center rounded-full bg-ink-900 ring-1 ring-white/10 shadow-glow sm:size-28">
                        <x-ui.icon name="wifi" class="size-11 text-white/90 animate-wobble sm:size-12" />
                        <svg class="pointer-events-none absolute inset-0 size-full text-rose-400/90" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                            <line x1="30" y1="30" x2="70" y2="70" stroke="currentColor" stroke-width="5" stroke-linecap="round" />
                        </svg>
                        {{-- scanning line --}}
                        <span class="pointer-events-none absolute inset-x-2 top-1/2 h-px bg-linear-to-r from-transparent via-brand-300/80 to-transparent animate-scan"></span>
                    </span>
                </div>

                {{-- collapsing signal bars --}}
                <div class="mt-7 flex items-end gap-1.5" aria-hidden="true">
                    <span class="h-3 w-1.5 rounded-full bg-brand-400 animate-bar"></span>
                    <span class="h-5 w-1.5 rounded-full bg-brand-400 animate-bar" style="animation-delay: 0.18s"></span>
                    <span class="h-7 w-1.5 rounded-full bg-brand-400 animate-bar" style="animation-delay: 0.36s"></span>
                    <span class="h-9 w-1.5 rounded-full bg-brand-400 animate-bar" style="animation-delay: 0.54s"></span>
                </div>

                <h1 id="offline-title" class="mt-6 text-2xl font-extrabold tracking-tight text-white sm:text-3xl">
                    Yah, sinyalnya hilang
                </h1>
                <p id="offline-desc" class="mx-auto mt-3 max-w-sm text-sm leading-relaxed text-ink-300">
                    Koneksi internetmu terputus. Coba cek Wi-Fi atau data seluler kamu ya —
                    halaman ini bakal lanjut sendiri kok begitu sinyalnya balik.
                </p>

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:justify-center">
                    <button
                        type="button"
                        x-ref="retry"
                        @click="retry()"
                        :disabled="retrying"
                        class="btn btn-md btn-gradient w-full disabled:cursor-wait disabled:opacity-70 sm:w-auto"
                    >
                        <x-ui.icon name="arrow-path" class="size-4" ::class="retrying && 'animate-spin'" x-show="retrying" />
                        <x-ui.icon name="arrow-path" class="size-4" x-show="!retrying" />
                        <span x-text="retrying ? 'Mencoba…' : 'Coba lagi'"></span>
                    </button>

                    <button
                        type="button"
                        @click="dismiss()"
                        class="btn btn-md w-full border border-white/15 bg-white/10 text-white backdrop-blur transition hover:bg-white/20 sm:w-auto"
                    >
                        Lihat halaman
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ========================= REOPEN BADGE ========================= --}}
    <button
        type="button"
        x-show="!online && dismissed"
        x-transition
        @click="reopen()"
        class="fixed bottom-5 left-5 z-[100] inline-flex items-center gap-2 rounded-full border border-rose-400/30 bg-ink-950/90 px-4 py-2.5 text-sm font-semibold text-rose-200 shadow-lift backdrop-blur-xl transition hover:border-rose-300/50 hover:text-white"
        aria-label="Koneksi terputus — buka lagi"
    >
        <span class="relative flex size-2">
            <span class="absolute inline-flex size-full animate-ping rounded-full bg-rose-400 opacity-75"></span>
            <span class="relative inline-flex size-2 rounded-full bg-rose-500"></span>
        </span>
        Koneksi terputus
    </button>
</div>
