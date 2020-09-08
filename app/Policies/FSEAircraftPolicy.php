<?php

namespace App\Policies;

use App\User;
use App\FSEAircraft;
use Illuminate\Auth\Access\HandlesAuthorization;

class FSEAircraftPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the f s e aircraft.
     *
     * @param  \App\User  $user
     * @param  \App\FSEAircraft  $fSEAircraft
     * @return mixed
     */
    public function view(User $user, FSEAircraft $fSEAircraft)
    {
        //
    }

    /**
     * Determine whether the user can create f s e aircrafts.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the f s e aircraft.
     *
     * @param  \App\User  $user
     * @param  \App\FSEAircraft  $fSEAircraft
     * @return mixed
     */
    public function update(User $user, FSEAircraft $fSEAircraft)
    {
        //
    }

    /**
     * Determine whether the user can delete the f s e aircraft.
     *
     * @param  \App\User  $user
     * @param  \App\FSEAircraft  $fSEAircraft
     * @return mixed
     */
    public function delete(User $user, FSEAircraft $fSEAircraft)
    {
        //
    }
}
