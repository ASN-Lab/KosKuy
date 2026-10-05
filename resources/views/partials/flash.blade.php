@php
    $flashes = array_filter([
        'success' => session('success'),
        'status' => session('status'),
        'error' => session('error'),
    ]);
@endphp

@if ($flashes)
    <div class="pointer-events-none fixed inset-x-0 top-4 z-90 flex flex-col items-center gap-2 px-4 sm:items-end sm:pr-6">
        @foreach ($flashes as $type => $message)
            @php
                $tones = [
                    'success' => ['bg-accent-600', 'check-solid'],
                    'status' => ['bg-brand-600', 'information-circle-solid'],
                    'error' => ['bg-rose-600', 'exclamation-triangle'],
                ];
                [$tone, $icon] = $tones[$type] ?? $tones['status'];
            @endphp

            <div
                role="status"
                class="pointer-events-auto flex w-full max-w-md items-start gap-3 rounded-2xl px-4 py-3.5 text-sm font-semibold text-white shadow-lift"
                x-data="{ show: true }"
                x-show="show"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
            >
                <span class="{{ $tone }} grid size-7 shrink-0 place-items-center rounded-full">
                    <x-ui.icon :name="$icon" class="size-4" />
                </span>

                <p class="min-w-0 flex-1 leading-relaxed">{{ $message }}</p>

                <button
                    type="button"
                    @click="show = false"
                    class="-mt-0.5 -mr-1 shrink-0 rounded-lg p-1 transition hover:bg-white/15"
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