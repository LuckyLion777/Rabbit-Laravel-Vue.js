<?php

namespace App\Jobs;

use App\Handlers\ImageHandler;
use App\Jobs\Job;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class CreateUserJob extends Job implements SelfHandling
{
    use InteractsWithQueue, SerializesModels;

    private $name;
    private $email;
    private $password;
    private $role;
    /**
     * @var
     */
    private $image;
    private $trial_ends;

    /**
     * Create a new job instance.
     *
     * @param $name
     * @param $email
     * @param $role
     * @param $image
     * @param $password
     * @param $trial_ends
     */
    public function __construct($name, $email, $role, $image, $password, $trial_ends)
    {
        $this->name     = $name;
        $this->email    = $email;
        $this->password = $password;
        $this->role     = $role;
        $this->image    = $image;
        $this->trial_ends = $trial_ends;
    }

    /**
     * Execute the job.
     *
     * @param UserRepository $users
     * @param ImageHandler $handler
     * @return \App\Models\User
     */
    public function handle(UserRepository $users, ImageHandler $handler)
    {
        $image = $handler->prepareImageUsingTemp($this->image, 'users');

        $user = $users->create($this->name, $this->email, $image['name'], bcrypt($this->password), $this->trial_ends);
        if ($this->role)
            $user->roles()->attach($this->role);

        return $user;
    }
}
