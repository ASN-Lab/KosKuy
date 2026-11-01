@extends('layouts.app')

@section('title', 'Checkout — ' . $product->title . ' | KosKuy')

@section('content')
<div class="container-page py-10">
    <div class="max-w-xl mx-auto">
        <a href="{{ route('thrift.show', $product->id) }}" class="text-sm font-semibold text-accent-700 hover:underline">&larr; Kembali ke barang</a>

        <div class="card p-8 mt-4">
            <h1 class="text-2xl font-bold mb-1">{{ $product->title }}</h1>
            <p class="text-lg font-semibold text-accent-700 mb-6">{{ rupiah($product->price) }}</p>

            @if (session('success'))
                <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-4">{{ session('success') }}</div>
            @endif
            @if (session('error'))
                <div class="bg-red-50 text-red-700 p-3 rounded-lg mb-4">{{ session('error') }}</div>
            @endif

            @if (! $payment)
                {{-- Step 1: pilih metode --}}
                <form method="POST" action="{{ route('thrift.checkout.store', $product->id) }}">
                    @csrf
                    <p class="text-sm font-semibold text-ink-700 mb-3">Pilih metode pembayaran</p>
                    <div class="space-y-3 mb-6">
                        <label class="flex items-center gap-3 p-4 border border-ink-200 rounded-xl cursor-pointer hover:border-accent-500">
                            <input type="radio" name="method" value="transfer_bank" checked class="size-4">
                            <span class="font-semibold">Transfer Bank</span>
                        </label>
                        <label class="flex items-center gap-3 p-4 border border-ink-200 rounded-xl cursor-pointer hover:border-accent-500">
                            <input type="radio" name="method" value="cash_on_pickup" class="size-4">
                            <span class="font-semibold">COD (Bayar di Tempat)</span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary w-full">Konfirmasi Pesanan</button>
                </form>
            @else
                {{-- Step 2: instruksi pembayaran --}}
                <div class="rounded-xl bg-ink-50 p-4 mb-6">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-ink-500">Metode</span>
                        <span class="font-semibold">{{ $payment->method === 'transfer_bank' ? 'Transfer Bank' : 'COD' }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-ink-500">Status</span>
                        <span class="font-semibold">{{ $payment->status }}</span>
                    </div>
                </div>

                @if ($payment->method === 'transfer_bank')
                    @php $bank = $product->seller; @endphp
                    <p class="text-sm font-semibold mb-2">Transfer ke rekening penjual:</p>
                    <div class="rounded-xl border border-ink-200 p-4 mb-4 text-sm">
                        <p class="font-bold">{{ $bank->bank_name ?: 'Belum diatur penjual' }}</p>
                        <p>{{ $bank->bank_number }}</p>
                        <p class="text-ink-500">a.n. {{ $bank->bank_holder ?: $bank->name }}</p>
                        <hr class="my-3 border-ink-100">
                        <p class="flex justify-between"><span>Nominal</span><span class="font-bold">{{ rupiah($payment->amount + $payment->unique_code) }}</span></p>
                        <p class="text-xs text-ink-400 mt-1">Termasuk kode unik {{ $payment->unique_code }} untuk verifikasi.</p>
                    </div>

                    @if ($payment->proof_path)
                        <div class="bg-green-50 text-green-700 p-3 rounded-lg mb-4 text-sm">Bukti terkirim, menunggu verifikasi admin.</div>
                    @else
                        <form method="POST" action="{{ route('thrift.checkout.proof', $product->id) }}" enctype="multipart/form-data">
                            @csrf
                            <label class="block text-sm font-semibold mb-2">Upload bukti transfer</label>
                            <input type="file" name="proof" accept="image/*" required class="field w-full mb-4">
                            <button type="submit" class="btn btn-primary w-full">Kirim Bukti</button>
                        </form>
                    @endif
                @else
                    <div class="bg-accent-50 text-accent-800 p-4 rounded-xl text-sm">
                        Bayar tunai saat pengambilan barang. Hubungi penjual untuk menyepakati waktu dan tempat.
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
