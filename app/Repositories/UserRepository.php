<?php


namespace App\Repositories;


use App\Models\User;

class UserRepository extends Repository
{
    protected $createFromFields = ['name', 'email', 'image', 'password', 'trial_ends'];
    protected $updateFromFields = ['name', 'email', 'image', 'password', 'trial_ends'];

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->object = $user;
    }

}
