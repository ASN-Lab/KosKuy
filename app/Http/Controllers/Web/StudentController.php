<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ThriftProduct;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function profile()
    {
        $user = auth()->user();

        return view('student.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
        ]);

        if (filled($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function bookings()
    {
        $bookings = auth()->user()->bookings()->with('kos')->latest()->get();

        return view('student.bookings', compact('bookings'));
    }

    public function purchases()
    {
        $purchases = ThriftProduct::where('buyer_id', auth()->id())
            ->with('seller')
            ->latest()
            ->get();

        return view('student.purchases', compact('purchases'));
    }
}