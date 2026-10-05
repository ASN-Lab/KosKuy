<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    /**
     * The subscriber list is kept in the cache so the footer form works
     * end-to-end without an extra table. Swap in a real table + model once the
     * newsletter feature needs per-subscriber records.
     */
    public function __invoke(Request $request): JsonResponse|RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = Str::lower($data['email']);
        $key = 'newsletter:subscribers';
        $subscribers = Cache::get($key, []);

        if (! in_array($email, $subscribers, true)) {
            $subscribers[] = $email;
            Cache::put($key, $subscribers, now()->addYear());
        }

        Log::info('Newsletter subscriber recorded', ['email' => $email]);

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Berhasil! Cek inbox kamu untuk konfirmasi.',
            ]);
        }

        return back()->with('success', 'Berhasil! Cek inbox kamu untuk konfirmasi.');
    }
}