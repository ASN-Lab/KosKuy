{{--
    Registered on the Filament panel's BODY_END hook (see AdminPanelProvider).

    Self-contained on purpose: the panel does not load resources/css/app.css, so
    the storefront's Tailwind utilities (`animate-signal-ring`, `bg-ink-950`, the
    `btn` classes…) do not exist here. The scene therefore ships its own scoped
    CSS and inlines the SVGs through App\Support\Icons. The Alpine state comes
    from the companion heading script (filament.offline-script).
--}}
<style>
    [x-cloak] { display: none !important; }

    .kk-offline {
        position: fixed; inset: 0; z-index: 9999;
        display: grid; place-items: center; padding: 1.25rem;
        background: rgba(5, 6, 15, .96);
        -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px);
        color: #e2e8f0; text-align: center;
    }
    .kk-offline * { box-sizing: border-box; }
    .kk-offline svg { display: block; width: 1em; height: 1em; }

    .kk-offline__glow { position: absolute; border-radius: 9999px; filter: blur(70px); pointer-events: none; }
    .kk-offline__glow--a { top: -6rem; right: -3rem; width: 22rem; height: 22rem; background: rgba(99, 102, 241, .35); }
    .kk-offline__glow--b { bottom: -7rem; left: 1rem; width: 22rem; height: 22rem; background: rgba(16, 185, 129, .22); }
    .kk-offline__grid {
        position: absolute; inset: 0; pointer-events: none; opacity: .5;
        background-image:
            linear-gradient(to right, rgba(255, 255, 255, .07) 1px, transparent 1px),
            linear-gradient(to bottom, rgba(255, 255, 255, .07) 1px, transparent 1px);
        background-size: 56px 56px;
    }
    .kk-offline__inner { position: relative; width: 100%; max-width: 26rem; }

    .kk-offline__badge {
        display: inline-flex; align-items: center; gap: .4rem; margin-bottom: 1.75rem;
        border-radius: 9999px; background: rgba(255, 255, 255, .9); color: #334155;
        padding: .3rem .7rem; font-size: .75rem; font-weight: 700;
    }
    .kk-offline__badge svg { width: .9rem; height: .9rem; color: #fb7185; }

    .kk-offline__scene { position: relative; display: grid; place-items: center; width: 11rem; height: 11rem; margin: 0 auto; }
    .kk-offline__ring {
        position: absolute; inset: .5rem; border-radius: 9999px;
        border: 1px solid rgba(99, 102, 241, .4);
        animation: kk-signal-ring 3.2s ease-out infinite;
    }
    .kk-offline__sweep {
        position: absolute; inset: 1rem; border-radius: 9999px;
        background: conic-gradient(from 0deg, transparent 0deg, rgba(99, 102, 241, .35) 65deg, transparent 135deg);
        animation: kk-signal-sweep 5s linear infinite;
    }
    .kk-offline__core {
        position: relative; display: grid; place-items: center; width: 6rem; height: 6rem; border-radius: 9999px;
        background: #0b1020; border: 1px solid rgba(255, 255, 255, .1); color: #f8fafc;
        box-shadow: 0 0 0 1px rgba(99, 102, 241, .16), 0 18px 46px -14px rgba(99, 102, 241, .42);
    }
    .kk-offline__core svg { width: 2.5rem; height: 2.5rem; animation: kk-wobble 5.5s ease-in-out infinite; }
    .kk-offline__core--ok { color: #6ee7b7; }
    .kk-offline__core--ok svg { animation: none; }
    .kk-offline__slash { position: absolute; inset: 0; width: 100%; height: 100%; color: rgba(251, 113, 133, .9); }
    .kk-offline__scan {
        position: absolute; left: .5rem; right: .5rem; top: 50%; height: 1px;
        background: linear-gradient(to right, transparent, rgba(165, 180, 252, .8), transparent);
        animation: kk-scan 4s ease-in-out infinite;
    }
    .kk-offline__cloud { position: absolute; color: rgba(255, 255, 255, .14); animation: kk-drift 9s ease-in-out infinite; }
    .kk-offline__cloud svg { width: 100%; height: 100%; }
    .kk-offline__cloud--a { top: .5rem; left: .25rem; width: 2.5rem; height: 2.5rem; }
    .kk-offline__cloud--b { bottom: 1.5rem; right: .25rem; width: 3.5rem; height: 3.5rem; color: rgba(255, 255, 255, .1); animation-delay: 1.6s; }

    .kk-offline__bars { display: flex; align-items: flex-end; justify-content: center; gap: .375rem; margin-top: 1.75rem; }
    .kk-offline__bars span { width: .375rem; border-radius: 9999px; background: #818cf8; animation: kk-bar 1.7s ease-in-out infinite; }

    .kk-offline__title { margin: 1.5rem 0 0; font-size: 1.5rem; font-weight: 800; color: #fff; letter-spacing: -.01em; }
    .kk-offline__text { margin: .75rem auto 0; max-width: 20rem; font-size: .875rem; line-height: 1.6; color: #94a3b8; }

    .kk-offline__actions { display: flex; flex-direction: column; gap: .75rem; margin-top: 2rem; }
    @media (min-width: 640px) { .kk-offline__actions { flex-direction: row; justify-content: center; } }
    .kk-offline__btn {
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        height: 2.75rem; padding: 0 1.25rem; border: 0; border-radius: 1rem;
        font: inherit; font-size: .875rem; font-weight: 600; cursor: pointer; text-decoration: none;
    }
    .kk-offline__btn svg { width: 1rem; height: 1rem; }
    .kk-offline__btn--primary { color: #fff; background-image: linear-gradient(120deg, #4f46e5, #7c3aed, #4f46e5); background-size: 200% 100%; }
    .kk-offline__btn--primary:hover { background-position: 100% 50%; }
    .kk-offline__btn--ghost { color: #fff; background: rgba(255, 255, 255, .1); border: 1px solid rgba(255, 255, 255, .15); }
    .kk-offline__btn--ghost:hover { background: rgba(255, 255, 255, .2); }
    .kk-offline__btn:disabled { opacity: .7; cursor: wait; }
    .kk-offline__spin svg { animation: kk-spin 1s linear infinite; }

    .kk-offline__pill {
        position: fixed; bottom: 1.25rem; left: 1.25rem; z-index: 9999;
        display: inline-flex; align-items: center; gap: .5rem;
        border-radius: 9999px; border: 1px solid rgba(251, 113, 133, .3);
        background: rgba(5, 6, 15, .9); padding: .625rem 1rem;
        font: inherit; font-size: .875rem; font-weight: 600; color: #fecdd3; cursor: pointer;
    }
    .kk-offline__dot { position: relative; width: .5rem; height: .5rem; border-radius: 9999px; background: #f43f5e; }
    .kk-offline__dot::after {
        content: ""; position: absolute; inset: 0; border-radius: 9999px; background: #f43f5e;
        opacity: .75; animation: kk-ping 1.2s cubic-bezier(0, 0, .2, 1) infinite;
    }

    @keyframes kk-signal-ring { 0% { transform: scale(.4); opacity: 0; } 30% { opacity: .6; } 100% { transform: scale(1.9); opacity: 0; } }
    @keyframes kk-signal-sweep { from { transform: rotate(0); } to { transform: rotate(360deg); } }
    @keyframes kk-drift { 0%, 100% { transform: translate(0, 0); } 50% { transform: translate(20px, -6px); } }
    @keyframes kk-wobble { 0%, 100% { transform: rotate(-3.5deg); } 50% { transform: rotate(3.5deg); } }
    @keyframes kk-bar { 0%, 100% { transform: translateY(0); opacity: .3; } 50% { transform: translateY(-3px); opacity: 1; } }
    @keyframes kk-scan { 0% { transform: translateY(120%); opacity: 0; } 30%, 70% { opacity: 1; } 100% { transform: translateY(-120%); opacity: 0; } }
    @keyframes kk-spin { to { transform: rotate(360deg); } }
    @keyframes kk-ping { 0% { transform: scale(1); opacity: .75; } 75%, 100% { transform: scale(2.2); opacity: 0; } }
    @media (prefers-reduced-motion: reduce) {
        .kk-offline__ring, .kk-offline__sweep, .kk-offline__core svg, .kk-offline__cloud,
        .kk-offline__bars span, .kk-offline__scan, .kk-offline__spin svg,
        .kk-offline__dot::after { animation: none !important; }
    }
</style>

<div x-data="offlineScreen" x-cloak @keydown.escape.window="dismiss()">
    <div class="kk-offline" x-show="(!online && !dismissed) || restored" x-transition.opacity.duration.300ms>
        <div class="kk-offline__grid" aria-hidden="true"></div>
        <div class="kk-offline__glow kk-offline__glow--a" aria-hidden="true"></div>
        <div class="kk-offline__glow kk-offline__glow--b" aria-hidden="true"></div>

        <div class="kk-offline__inner" role="alertdialog" aria-modal="true" aria-labelledby="kk-offline-title">
            {{-- Restored --}}
            <div x-show="restored" x-cloak>
                <span class="kk-offline__core kk-offline__core--ok">{!! \App\Support\Icons::svg('check-badge') !!}</span>
                <h1 id="kk-offline-title" class="kk-offline__title">Sinyal kembali!</h1>
                <p class="kk-offline__text">Koneksi internetmu tersambung lagi. Sebentar ya, halaman dilanjutkan otomatis.</p>
            </div>

            {{-- Offline --}}
            <div x-show="!restored">
                <span class="kk-offline__badge">{!! \App\Support\Icons::svg('wifi') !!} Tidak ada koneksi</span>

                <div class="kk-offline__scene">
                    <span class="kk-offline__cloud kk-offline__cloud--a">{!! \App\Support\Icons::svg('cloud') !!}</span>
                    <span class="kk-offline__cloud kk-offline__cloud--b">{!! \App\Support\Icons::svg('cloud') !!}</span>
                    <span class="kk-offline__ring"></span>
                    <span class="kk-offline__ring" style="animation-delay: 1.07s"></span>
                    <span class="kk-offline__ring" style="animation-delay: 2.14s"></span>
                    <span class="kk-offline__sweep"></span>
                    <span class="kk-offline__core">
                        {!! \App\Support\Icons::svg('wifi') !!}
                        <svg class="kk-offline__slash" viewBox="0 0 100 100" fill="none" aria-hidden="true">
                            <line x1="30" y1="30" x2="70" y2="70" stroke="currentColor" stroke-width="5" stroke-linecap="round" />
                        </svg>
                        <span class="kk-offline__scan"></span>
                    </span>
                </div>

                <div class="kk-offline__bars" aria-hidden="true">
                    <span style="height: .75rem"></span>
                    <span style="height: 1.25rem; animation-delay: .18s"></span>
                    <span style="height: 1.75rem; animation-delay: .36s"></span>
                    <span style="height: 2.25rem; animation-delay: .54s"></span>
                </div>

                <h1 id="kk-offline-title" class="kk-offline__title">Yah, sinyalnya hilang</h1>
                <p class="kk-offline__text">Koneksi internetmu terputus. Coba cek Wi-Fi atau data seluler kamu ya — halaman ini bakal lanjut sendiri kok begitu sinyalnya balik.</p>

                <div class="kk-offline__actions">
                    <button type="button" class="kk-offline__btn kk-offline__btn--primary" @click="retry()" :disabled="retrying">
                        <span class="kk-offline__spin" x-show="retrying" style="display: inline-flex">{!! \App\Support\Icons::svg('arrow-path') !!}</span>
                        <span x-show="!retrying" style="display: inline-flex">{!! \App\Support\Icons::svg('arrow-path') !!}</span>
                        <span x-text="retrying ? 'Mencoba…' : 'Coba lagi'"></span>
                    </button>
                    <button type="button" class="kk-offline__btn kk-offline__btn--ghost" @click="dismiss()">Lihat halaman</button>
                </div>
            </div>
        </div>
    </div>

    <button type="button" class="kk-offline__pill" x-show="!online && dismissed" x-transition @click="reopen()">
        <span class="kk-offline__dot"></span>
        Koneksi terputus
    </button>
</div>
