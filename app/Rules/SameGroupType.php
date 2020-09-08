<?php

namespace App\Rules;

use App\Group;
use App\Service;
use Illuminate\Contracts\Validation\Rule;

class SameGroupType implements Rule
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
        $group = Group::findOrFail(request()->group_id);
        $service = Service::findOrFail(request()->service_id);

        $typeCheck = false;
        if ($group->type == 'Personal' && ($service->group_type == 'Personal' || $service->group_type == 'both')) {
            $typeCheck = true;
        } elseif ($group->type == 'Group' && ($service->group_type == 'Group' || $service->group_type == 'both')) {
            $typeCheck = true;
        }

        return $typeCheck;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'Wrong group type for subscription selected. Make sure you select a group service for a group and a personal service for a personal account';
    }
}
