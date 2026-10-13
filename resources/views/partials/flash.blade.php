@php
    $flashes = array_filter([
        'success' => session('success'),
        'status' => session('status'),
        'error' => session('error'),
    ]);
@endphp

@if ($flashes)
    <div class="pointer-events-none fixed inset-x-0 top-24 z-90 flex flex-col items-center gap-2 px-4 sm:items-end sm:pr-6" aria-live="polite">
        @foreach ($flashes as $type => $message)
            @php
                $tones = [
                    'success' => ['bg-accent-600', 'check-solid', 'border-accent-200', 'text-accent-900'],
                    'status' => ['bg-brand-600', 'information-circle-solid', 'border-brand-200', 'text-brand-900'],
                    'error' => ['bg-rose-600', 'exclamation-triangle', 'border-rose-200', 'text-rose-900'],
                ];
                [$iconBg, $icon, $border, $text] = $tones[$type] ?? $tones['status'];
            @endphp

            <div
                role="status"
                class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-2xl border bg-white px-4 py-3.5 text-sm font-semibold shadow-lift {{ $border }} {{ $text }}"
                x-data="{ show: true }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <span class="{{ $iconBg }} grid size-8 shrink-0 place-items-center rounded-full text-white shadow-soft">
                    <x-ui.icon :name="$icon" class="size-4" />
                </span>

                <p class="min-w-0 flex-1 leading-relaxed">{{ $message }}</p>

                <button
                    type="button"
                    @click="show = false"
                    class="-mt-0.5 -mr-1 shrink-0 rounded-lg p-1 text-ink-400 transition hover:bg-ink-100 hover:text-ink-700"
                    aria-label="Tutup notifikasi"
                >
                    <x-ui.icon name="x-mark" class="size-4" />
                </button>
            </div>
        @endforeach
    </div>
@endif

@if ($errors->any() && ! $errors->has('email'))
    <div class="border-b border-rose-100 bg-rose-50">
        <div class="container-page py-4">
            <p class="flex items-start gap-2.5 text-sm font-semibold text-rose-700">
                <x-ui.icon name="exclamation-triangle" class="mt-0.5 size-4.5 shrink-0" />
                <span>{{ $errors->first() }}</span>
            </p>
        </div>
    </div>
@endif