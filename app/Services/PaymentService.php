<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Model;

class PaymentService
{
    /**
     * Inisialisasi atau update pilihan pembayaran.
     */
    public function choose(Model $payable, string $method, float $amount): Payment
    {
        $payment = $payable->payment;

        if (!$payment) {
            $uniqueCode = ($method === Payment::METHOD_TRANSFER) ? rand(1, 999) : 0;
            return $payable->payment()->create([
                'method' => $method,
                'amount' => $amount,
                'unique_code' => $uniqueCode,
                'status' => $method === Payment::METHOD_CASH
                    ? Payment::STATUS_PENDING_PICKUP
                    : Payment::STATUS_UNPAID,
            ]);
        }

        // Jika ganti metode sebelum verifikasi dimulai
        if ($payment->status === Payment::STATUS_UNPAID || $payment->status === Payment::STATUS_PENDING_PICKUP) {
            $uniqueCode = ($method === Payment::METHOD_TRANSFER) ? rand(1, 999) : 0;
            $payment->update([
                'method' => $method,
                'unique_code' => $uniqueCode,
                'status' => $method === Payment::METHOD_CASH
                    ? Payment::STATUS_PENDING_PICKUP
                    : Payment::STATUS_UNPAID,
            ]);
        }

        return $payment;
    }
}