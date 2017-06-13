<?php

namespace App\Http\Controllers\Admin;

use App\Handlers\CloudinaryImageHandler;
use App\Handlers\ImageHandler;
use App\Http\Requests\AddReferralRequest;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\GetUpcomingRequest;
use App\Http\Requests\GetUsersRequest;
use App\Http\Requests\SetReferrerConfirmedRequest;
use App\Http\Requests\SetReferrerRequest;
use App\Http\Requests\UpdateSettingsRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UploadImageRequest;
use App\Jobs\CreateUserJob;
use App\Jobs\UpdateUserJob;
use App\Models\Filters\UsersFilter;
use App\Models\Litter;
use App\Models\RabbitBreeder;
use App\Models\RabbitKit;
use App\Models\SocialAccount;
use App\Models\Subscription;
use App\Models\User;
use App\Push\Contracts\Pusher as GenericPusher;
use App\Push\Message;
use App\Push\Web\Pusher;
use App\Repositories\UserRepository;

use Collective\Bus\Dispatcher;
use File;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Log;
use Auth;
use Minishlink\WebPush\VAPID;
use Stripe\Customer;
use Stripe\Plan;
use Stripe\Stripe;
use DB;

class AdminUsersController extends Controller
{

    /**
     * @var UserRepository
     */
    private $users;
    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * AdminUsersController constructor.
     * @param UserRepository $users
     * @param Dispatcher $dispatcher
     */
    public function __construct(UserRepository $users, Dispatcher $dispatcher)
    {
        $this->middleware('role:admin', ['only' => ['index','show','store','update','destroy','dashboard',
                                                    'sendMessage', 'su']]);
        $this->users = $users;

        $this->dispatcher = $dispatcher;
    }

    public function index(UsersFilter $filter, GetUsersRequest $request)
    {
        $users = $filter->filter(User::query(), 'allUsers', getenv('USERS_PER_PAGE'));
        return response()->json(['users' => $users]);
    }

    public function getCurrent()
    {
        return auth()->user();
    }

    public function store(CreateUserRequest $request)
    {
        return $this->dispatcher->dispatchFrom(CreateUserJob::class, $request);

    }

    public function show(User $user)
    {
        $roles = $user->roles->isEmpty() ? 0 : $user->roles()->first()->id;
        $user->setAttribute('role', $roles);

        return $user;
    }

    public function update(User $user, UpdateUserRequest $request)
    {
        $request['user'] = $user;

        $this->dispatcher->dispatchFrom(UpdateUserJob::class, $request);
    }

    public function destroy(User $user)
    {
        $this->users->delete($user);
    }

    public function getSettingsData(User $user)
    {
        return response()->json(['user' => $user, 'chains' => $user->breedChainsOrdered()->get()]);
    }

    public function sendMessage(User $user, Request $request, GenericPusher $pusher)
    {
        $this->validate($request, [
            'title' => 'string',
            'message' => 'required|string'
        ]);

        $message = new Message($request->input('title', 'Message from Admin'), $request->input('message'));
        return response()->json(['result' => $pusher->sendToUser($user, $message)]);
    }

    public function getCurrentSettingsData()
    {
        $user = auth()->user();
        return response()->json(['user' => $user, 'chains' => $user->breedChainsOrdered()->get()]);
    }


    public function referrals(Guard $guard)
    {
        $user = $guard->user();
        echo $user;
        /* @var $user User */
        return response()->json([
            'referrals' => collect($user->referrals)->map(function (User $referral) {
                return $referral->asReferral();
            }),
            'referred_by' => $user->referredBy ? $user->referredBy->email : '',
            'referrals_credit' => ($user->referrals_credited + min(collect($user->referrals)->sum(function (User $referral) {
                return !$referral->subscribed() || $referral->referrer_credited_at ? 0 : 500;
            }), $user->getReferralsCap())) * .01
        ]);
    }

    public function socials(Guard $guard)
    {
        $user = $guard->user();
        /* @var $user User */
        return response()->json([
            'social_accounts' => $user->socialAccounts->map(function (SocialAccount $social) {
                return [
                    'id' => $social->getKey(),
                    'provider' => ucfirst($social->provider),
                    'username' => $social->username,
                    'avatar' => $social->data['avatar'],
                ];
            })
        ]);
    }

    public function disconnectSocial(Guard $guard, SocialAccount $social)
    {
        $user = $guard->user();
        /* @var $user User */
        if ($social->user_id == $user->id) {
            if (!$user->password && $user->socialAccounts()->count() == 1) {
                return response()->json(['error' => 'You cannot disconnect your last social account if you have no password'], 403);
            }

            $social->user()->associate(null);
            $social->save();
        }

        return response()->json([]);
    }

    public function addWebPushEndpoint(Request $request, Pusher $pusher)
    {
        $this->validate($request, [
            'endpoint' => 'required|string',
            'reset-creds' => 'required|bool',
            'publicKey' => 'string',
            'authToken' => 'string'
        ]);

        $data = [
            'endpoint' => $request->input('endpoint'),
            'client_public_key' => $request->input('publicKey'),
            'auth_token' => $request->input('authToken')
        ];

        $pusher->register($request->user(), $data, !!$request->input('reset-creds'));

        return response()->json([
            'success' => true
        ]);
    }

    public function sendTestNotification(Guard $guard, Pusher $pusher)
    {
        $message = new Message(
            'Hutch Test Notification',
            'Hutch Notifications are enabled. Thanks!'
        );
        $message->url = null;
        $pusher->sendToUser($guard->user(), $message);
    }

    public function addReferral(AddReferralRequest $request)
    {
        $referred = User::where('email', $request->get('email'))->first();
        /* @var $referred User */
        $referred->askConfirmReferrer($request->user());
        return response()->json([]);
    }

    public function setReferrerConfirmed(SetReferrerConfirmedRequest $request)
    {
//        $user = User::where('email', $request->query('me'))->first();
//        $user->referredBy()->associate(User::where('email', $request->query('email'))->first());
//        $user->save();
//        $request->session()->set('callout_success', 'You have successfully confirmed your referrer, thank you!');
        if (!$request->user()) {
            $request->session()->set('referrer_confirm', [
                'me' => $request->query('me'),
                'referrer' => $request->query('email')
            ]);
            return redirect('/login');
        }
        return redirect('/#!/account?referrer=' . $request->query('email'));
    }

    public function setReferrer(SetReferrerRequest $request)
    {
        $user = $request->user();
        $user->referredBy()->associate(User::where('email', $request->get('email'))->first());
        $user->save();
        return response()->json([]);
    }

    public function setReferrerDry(SetReferrerRequest $request)
    {
        return response()->json([]);
    }

    public function su(User $user)
    {
        \Auth::login($user, false);
        return redirect('/');
    }

    public function settings(User $user)
    {
        return view('settings', compact('user'));
    }

    public function updateSettings(UpdateSettingsRequest $request, User $user, CloudinaryImageHandler $handler)
    {
        $user->name = $request->get('name');
        //$user->email = $request->get('email');
        if ($request->get('new_password')) {
            $user->password = bcrypt($request->get('new_password'));
        }
        $user->general_weight_units = $request->general_weight_units;
        $user->pedigree_number_generations = $request->pedigree_number_generations;
        $user->pedigree_rabbitry_information = $request->pedigree_rabbitry_information;

        if($request->digest_day == -1){
            $user->digest_day = -1;
        } else if($user->digest_day != $request->digest_day){
            $user->digest_day = $request->digest_day;
            $user->last_digest_at = null;
        }

        $image       = $handler->prepareImageUsingTemp($request->get('pedigree_logo'), 'logo');
        $user->pedigree_logo = $image['name'];
        $user->currency = $request->currency;
        $user->date_format = $request->date_format;

        $user->update();
        $user->updateBreadChains($request->breedchains);

        return response()->json(['success' => ['User has been updated']], 200);
    }

    public function upcomingEvents(GetUpcomingRequest $request)
    {
        return auth()->user()->upcomingEvents()->take($request->get('count'))->get();
    }

    public function plans(Request $request)
    {
        $plans = auth()->user()->plans()->has('breeders', '>=', 2)->whereDoesntHave('events', function($query) {
            return $query->where('type', '=', 'litter')->where('icon', 'fa-balance-scale bg-yellow')->whereNotNull('holderName');
        })->where('date', '>=', Carbon::now()->subDays(60))->where('date', '<=', Carbon::today())->where('missed', '0');
        if($request->has('perPage')){
            return $plans->paginate($request->get('perPage'));
        }
        return $plans->get();
    }

    public function dashboard()
    {
        Stripe::setApiKey(config('services.stripe.secret'));

        $counts = array_fill_keys(['premium_yr', 'basic_yr', 'mini_yr', 'forever'], 0);
        foreach ($counts as $plan => $_) {
            $counts[$plan] = Subscription::all(['plan' => $plan, 'include' => ['total_count']])->total_count;
        }

        return response()->json([
            'usersPremium' => $counts['premium_yr'],
            'usersBasic' => $counts['basic_yr'],
            'usersMini' => $counts['mini_yr'],
            'usersForever' => $counts['forever'],
            'breedersTotal'  => RabbitBreeder::count(),
            'littersTotal'  => Litter::count(),
            'kitsTotal'  => RabbitKit::count(),
        ], 200);
    }


    /**
     * When user clicks on events round in the right part of the schedule
     * @param Request $request
     * @return mixed
     */
    public function events(Request $request)
    {
        $user = Auth::user();
        if ($request->has('weekStart')){
            $date = Carbon::createFromFormat(Auth::user()->getDateFormatPHP(), $request->get('weekStart'));
            $data = $user->dateWeeklyEvents($date)->where('type','general')->get();
            return $data;
        }
        return $user->entireEvents;
    }

    public function subscribe(Guard $auth)
    {
        $user = $auth->user();
        /* @var $user User */
        $user->newSubscription('premium', 'yearly')->create();
    }

    public function tourShowed(Guard $auth)
    {
        $user = $auth->user();
        /* @var $user User */
        $user->tourShowed = 1;
        $user->save();
    }

    public function getDeathReasons(Guard $auth)
    {
        $reasons = $auth->user()->getReasonForDeathStatistics();
        $reasons = array_map(function ($reason) {
            return (string) $reason;
        }, array_keys($reasons));
        return response()->json(['reasons' => $reasons]);
    }

    public function getDeathList(Guard $auth)
    {
        $reasons = $auth->user()->getDeathReasonsList(Auth::user()->id);

        return response()->json(['reasons' => $reasons]);
    }

    public function saveDeathReasonsList(Request $request)
    {
        if (isset($request->name)) {
            $name = $request->name;
        } else {
            $name = '';
        }

        DB::table('death_reasons')->insert(
            [
                'name' => $request->name,
                'user_id' => Auth::user()->id
            ]
        );

        return response()->json(['success' => ['Reason has been saved']], 200);
    }
}
