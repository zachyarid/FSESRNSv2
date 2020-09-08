<?php

namespace App\Rules;

use App\Subscription;
use Illuminate\Contracts\Validation\Rule;

class FullPayment implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $monthly_cost = Subscription::find(request()->subscription)->monthly_cost;

        return $monthly_cost == $value;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The :attribute must be the full month\'s payment.';
    }
}
