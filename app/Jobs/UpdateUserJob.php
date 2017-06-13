<?php

namespace App\Jobs;

use App\Handlers\ImageHandler;
use App\Jobs\Job;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateUserJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    private $password;
    private $user;
    private $role;
    private $name;
    private $email;
    private $trial_ends;
    /**
     * @var
     */
    private $image;

    public function __construct($user, $name, $email, $role, $image, $password, $trial_ends)
    {
        $this->password = $password;
        $this->user     = $user;
        $this->name     = $name;
        $this->email    = $email;
        $this->role     = $role;
        $this->image    = $image;
        $this->trial_ends = $trial_ends;
    }


    public function handle(UserRepository $repo, ImageHandler $handler)
    {
        $image = $handler->prepareImageUsingTemp($this->image, 'users');

        $password = $this->password ? bcrypt($this->password) : $this->user->password;

        $user = $repo->update($this->user, $this->name, $this->email, $image['name'], $password, $this->trial_ends);
        /* @var $user User */
        $user->roles()->detach();
        if ($this->role)
            $user->roles()->attach($this->role);
        $user->updateStripeCustomer();

        return $user;
    }
}
