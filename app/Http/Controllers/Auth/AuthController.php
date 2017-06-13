<?php

namespace App\Http\Controllers\Auth;

use App\Contracts\DeviceTracker;
use App\Http\Requests\SetEmailRequest;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

//use App\Http\Controllers\Auth\Request;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/#!/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => ['logout', 'addProvider', 'handleProviderCallback']]);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'max:255',
            'email' => 'required|email|confirmed|max:255|unique:users',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array $data
     * @return User
     */
    protected function create(array $data)
    {
        $user = new User([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => isset($data['password']) ? bcrypt($data['password']) : null,
        ]);
        $user->referred_by_id = \Session::get('inviter', null);
        $user->save();
        return $user;
    }

    public function redirectPath()
    {
        return property_exists($this, 'redirectTo') ? $this->redirectTo : '/home';
    }

    /*
        public function sendFailedLoginResponse(Request $request)
        {
            return \Response::json([
    //            'email' => $request->get($this->loginUsername()),
    //            'remember' => $request->get('remember'),
                'email'    => $this->getFailedLoginMessage(),
                'password' => "",
            ], 422);
        }
    */

    // Defined in an attempt to fix #19
    public function authenticated(Request $request, $user)
    {
        app(DeviceTracker::class)->associateWithUser($user);
        if (($confirm = $request->session()->pull('referrer_confirm')) && @$confirm['me'] == $user->email) {
            return redirect('/#!/account?referrer=' . $confirm['referrer']);
        }
        return redirect($this->redirectPath());
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        $user = $this->create($request->all());
        Auth::guard($this->getGuard())->login($user);
        app(DeviceTracker::class)->associateWithUser($user);
        \Mail::send('emails.welcome', compact('user'), function (Message $message) use ($user) {
            $message->subject('Getting started with Hutch');
            $message->to($user->email, $user->name);
        });

        return redirect(url('/#!/wizard/settings'));
    }

    public function redirectToProvider($provider)
    {
        return \Socialite::driver($provider)->redirect();
    }

    public function addProvider($provider)
    {
        \Session::set("social-add-{$provider}", "1");
        return \Socialite::driver($provider)->redirect();
    }

    /**
     * This method is called in callback when user returns from a social network
     * authentication procedure.
     *
     * @param Request $request
     * @param string $provider
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function handleProviderCallback(Request $request, $provider)
    {
        $social = $this->getSocialAccount(\Socialite::driver($provider)->user(), $provider);

        if (($user = \Auth::user()) && \Session::has("social-add-{$provider}")) {
            /* @var $user User */

            \Session::remove("social-add-{$provider}");

            if ($another = $social->user) {
                /* @var $another User */
                $another->mergeInto($user);
            } else {
                $user->socialAccounts()->save($social);
            }

            return redirect(url('/#!/account'));
        }

        if ($user = $social->user) {
            \Auth::login($user);
            // Device ID will be associated with user in $this->authenticate
            return $this->authenticated($request, $user);
        }

        if ($user = $this->createUserFromSocial($social, $social->email)) {
            \Auth::login($user);
            app(DeviceTracker::class)->associateWithUser($user);
            return redirect(url('/#!/wizard/settings'));
        }

        $request->session()->set("auth-social-{$social->id}", '1');
        // So, what we're doing here is: we store the social_account record
        // and show user page (looking like a wizard page) asking for an email.
        // If the user enters a valid email - THEN we create user account
        // and redirect them to the real wizard.
        return redirect(url('/wizard/email?social=' . $social->id));
    }

    public function setEmail(SetEmailRequest $request)
    {
        if (!$request->session()->has("auth-social-{$request->query('social')}")) {
            abort(403, 'Forbidden');
        }

        $social = SocialAccount::findOrFail($request->query('social'));
        if ($social->user) {
            abort(403, 'Forbidden');
        }

        if ($user = $this->createUserFromSocial($social, $request->input('email'))) {
            \Auth::login($user);
            return redirect(url('/#!/wizard/settings'));
        }
    }

    /**
     * Fetches from DB or creates a new social account record
     * for the specified social user.
     *
     * @param \Laravel\Socialite\Contracts\User $userData
     * @param string $provider
     * @return SocialAccount
     */
    private function getSocialAccount(\Laravel\Socialite\Contracts\User $userData, $provider)
    {
        $social = SocialAccount::where([
            'provider' => $provider,
            'provider_id' => $userData->getId(),
        ])->first();

        if (!$social) {
            $social = new SocialAccount();
            $social->provider = $provider;
            $social->provider_id = $userData->getId();
        }

        $social->username = $userData->getNickname() ?: $userData->getName();
        $social->email = $userData->getEmail();
        $social->data = (array) $userData;
        $social->save();

        return $social;
    }

    /**
     * This method creates a new social account but ONLY if the email address is provided
     * and is currently not in use.
     *
     * @param SocialAccount $account
     * @param string|null $email
     * @return User|null
     */
    private function createUserFromSocial(SocialAccount $account, $email)
    {
        if (!$email || User::where('email', $email)->count()) {
            return null;
        }

        $user = $this->create([
            'name' => $account->username,
            'email' => $email,
        ]);
        $user->socialAccounts()->save($account);
        return $user;
    }
}
