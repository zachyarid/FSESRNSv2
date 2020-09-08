<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpecialPayments\EditSpecialPaymentRequest;
use App\Http\Requests\SpecialPayments\NewSpecialPaymentRequest;
use App\SpecialPayment;
use Illuminate\Http\Request;

class SpecialPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = [
            'pageTitle' => 'My Special Payments',
            'specialPayments' => \Auth::user()->specialpayments,
        ];

        return view('pages.special-payments.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data = [
            'pageTitle' => 'Add Special Payment',
        ];

        return view('pages.special-payments.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\SpecialPayments\NewSpecialPaymentRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewSpecialPaymentRequest $request)
    {
        SpecialPayment::create([
            'user_id' => \Auth::id(),
            'group_id' => $request->group_id,
            'type' => $request->payment_type,
            'amount' => $request->payment_amount,
            'comment' => $request->comments,
            'frequency' => $request->frequency,
        ]);

        return redirect()->route('special-payments.index')->with('success_message', 'Special payment added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  SpecialPayment $specialPayment
     * @return \Illuminate\Http\Response
     */
    public function show(SpecialPayment $specialPayment)
    {
        $data = [
            'pageTitle' => 'View Special Payment',
            'specialPayment' => $specialPayment,
        ];

        return view('pages.special-payments.show', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  SpecialPayment $specialPayment
     * @return \Illuminate\Http\Response
     */
    public function edit(SpecialPayment $specialPayment)
    {
        $data = [
            'pageTitle' => 'Edit Special Payment',
            'specialPayment' => $specialPayment,
        ];

        return view('pages.special-payments.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\SpecialPayments\EditSpecialPaymentRequest  $request
     * @param  SpecialPayment $specialPayment
     * @return \Illuminate\Http\Response
     */
    public function update(EditSpecialPaymentRequest $request, SpecialPayment $specialPayment)
    {
        $specialPayment->amount = $request->payment_amount;
        $specialPayment->type = $request->payment_type;
        $specialPayment->group_id = $request->group_id;
        $specialPayment->frequency = $request->frequency;
        $specialPayment->comment = $request->comments;

        $specialPayment->save();

        return redirect()->route('special-payments.index')->with('success_message', 'Special payment updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  SpecialPayment $specialPayment
     * @return \Illuminate\Http\Response
     */
    public function destroy(SpecialPayment $specialPayment)
    {
        //
    }
}
