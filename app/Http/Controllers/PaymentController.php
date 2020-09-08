<?php

namespace App\Http\Controllers;

use App\Http\Requests\Payments\NewPaymentRequest;
use App\Payment;

class PaymentController extends Controller
{
    public function index()
    {
        $data = [
            'pageTitle' => 'My Payments',
            'payments' => \Auth::user()->payments,
        ];

        return view('pages.payments.index', $data);
    }

    public function create()
    {
        $data = [
            'pageTitle' => 'Make A Payment'
        ];

        return view('pages.payments.add', $data);
    }

    public function store(NewPaymentRequest $request)
    {
        Payment::create([
            'user_id' => \Auth::id(),
            'subscription_id' => $request->subscription,
            'amount' => $request->payment_amount,
            'payer' => $request->payment_group
        ]);

        // Job to verify payment

        return redirect()->route('payments.index')->with('success_message', 'Payment added. Payment verification process started');
    }
}
