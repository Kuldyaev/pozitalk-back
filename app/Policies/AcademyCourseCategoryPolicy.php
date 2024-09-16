<?php

namespace App\Policies;

use App\Models\AcademyCourseCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class AcademyCourseCategoryPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user)
    {
        return Response::allow();
    }

    /**
     * Determine whether the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(?User $user)
    {
        if(Auth()->user()->role_id == 1)
            return Response::deny();
        else
            return Response::allow();
    }


    public function update(?User $user, AcademyCourseCategory  $category)
    {
        if(Auth()->user()->role_id == 1)
            return Response::deny();
        else
            return Response::allow();
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(?User $user)
    {
        if(Auth()->user()->role_id == 1)
            return Response::deny();
        else
            return Response::allow();
    }

}
