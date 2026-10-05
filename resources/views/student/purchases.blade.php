@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-6">Riwayat Belanja Thrift</h1>

<div class="bg-white rounded-xl shadow-md overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 text-left">
            <tr>
                <th class="px-4 py-3">Produk</th>
                <th class="px-4 py-3">Harga</th>
                <th class="px-4 py-3">Kondisi</th>
                <th class="px-4 py-3">Penjual</th>
                <th class="px-4 py-3">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($purchases as $product)
                <tr class="border-t">
                    <td class="px-4 py-3">{{ $product->title }}</td>
                    <td class="px-4 py-3">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 capitalize">{{ $product->condition_status }}</td>
                    <td class="px-4 py-3">{{ $product->seller->name ?? '-' }}</td>
                    <td class="px-4 py-3"><span class="px-2 py-1 rounded text-xs bg-red-100 text-red-700">Terjual</span></td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-gray-400">Belum ada pembelian.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection