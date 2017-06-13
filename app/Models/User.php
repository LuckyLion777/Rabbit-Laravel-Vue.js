<?php

namespace App\Models;

use App\Models\Ledger\Category as LedgerCategory;
use App\Models\Ledger\Category;
use App\Models\Ledger\Entry as LedgerEntry;
use App\Models\Push\PushSubscription;
use App\Models\Subscription\Plan;
use App\Traits\CloudinaryImageAbleTrait;
use App\Traits\EventsTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Mail\Message;
use Illuminate\Support\Str;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Invoice;
use Stripe\Charge;
use Stripe\Customer;
use Zizaco\Entrust\Traits\EntrustUserTrait;

/**
 * @property LedgerEntry[]|Collection $ledger
 * @property PushSubscription[]|Collection $pushSubscriptions
 */
class User extends Authenticatable
{
    use EntrustUserTrait, CloudinaryImageAbleTrait, EventsTrait, Billable;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable     = [
        'name', 'email', 'password',
    ];
    protected $imagesFolder = 'users';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $appends     = [
        'weight_slug', 'subscription_status', 'trial_ends', 'date_format_php', 'date_format_js', 'date_format_js_short'
    ];

    protected $dates = ['last_digest_at', 'trial_ends_at'];


    public function breeders()
    {
        return $this->hasMany(RabbitBreeder::class);
    }

    public function litters()
    {
        return $this->hasMany(Litter::class);
    }


    public function rabbitKits()
    {
        return $this->hasMany(RabbitKit::class);
    }

    public function diedKits()
    {
        return $this->hasMany(RabbitKit::class)->where('survived', '=', 0);
    }

    public function plans()
    {
        return $this->hasMany(BreedPlan::class);
    }
    public function maleBreeders()
    {
        return $this->hasMany(RabbitBreeder::class)->where('sex', '=', 'buck');
    }

    public function femaleBreeders()
    {
        return $this->hasMany(RabbitBreeder::class)->where('sex', '=', 'doe');
    }

    public function breedChains()
    {
        return $this->hasMany(BreedChain::class);
    }

    public function devices()
    {
        return $this->hasMany(Device::class);
    }

    public function updateBreadChains($request) {
        foreach ($this->breedChains as $bc) {
            if(!isset($request['days'][$bc->id])) $bc->delete();
        }

        if (isset($request['name'])) {
            $type = 'breeder';

            $names = $request['name'];
            uksort($names, function ($d1, $d2) use ($request) {
                return $request['days'][$d1] - $request['days'][$d2];
            });

            foreach ($names as $k => $v) {
                if ($new = !($chain = $this->breedChains()->where('id', $k)->first())) {
                    $chain = new BreedChain();
                    $chain->user()->associate($this);
                }

                $chain->name = $v;
                $chain->days = $request['days'][$k];
                $chain->icon = $request['icon'][$k];
                $chain->save();

                if ($new) {
                    $chain->addToExistingPlans($type);
                }

                if ($chain->icon =='fa-birthday-cake bg-green') {
                    $type = 'litter';
                }
            }
        }
    }


    public function getWeightSlugAttribute()
    {
        /*
         * short label: lbs for pounds
oz for ounces
g for grams
kg for kilograms
         */
        switch($this->general_weight_units){
            case 'Grams':
                return 'g';
                break;
            case 'Ounces':
                return 'oz';
                break;
            case 'Pounds':
                return 'lbs';
                break;
            case 'Pound/Ounces':
                return '';
                break;
            case 'Kilograms':
                return 'kg';
                break;
        }
    }


    public function breedChainsOrdered()
    {
        if (!$this->breedChains->count()){

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="breed";
            $b->days=0;
            $b->icon='fa-venus-mars bg-blue original';
            $b->save();

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="pregnancy check";
            $b->days=15;
            $b->icon='fa-check bg-maroon';
            $b->save();

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="nest box";
            $b->days=26;
            $b->icon='fa fa-inbox icon-circle bg-purple';
            $b->save();

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="kindle/birth";
            $b->days=30;
            $b->icon='fa-birthday-cake bg-green original';
            $b->save();

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="weigh1";
            $b->days=65;
            $b->icon='fa-balance-scale bg-yellow first-weight';
            $b->save();

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="weigh2";
            $b->days=80;
            $b->icon='fa-balance-scale bg-yellow';
            $b->save();

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="weigh3";
            $b->days=95;
            $b->icon='fa-balance-scale bg-yellow';
            $b->save();

            $b = new BreedChain();
            $b->user_id = $this->id;
            $b->name="butcher";
            $b->days=100;
            $b->icon='fa-cutlery bg-red';
            $b->save();

            // Just in case
            $this->load('breedChains');
        }
        return $this->breedChains()->orderBy('days','asc');
    }

    public function ledger()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function ledgerCategories()
    {
        return $this->hasMany(LedgerCategory::class);
    }

    public function breederCategories()
    {
        return $this->hasMany(RabbitBreederCategory::class);
    }

    /**
     * Breeder categories available for the user - his own and shared (special) ones
     * @return RabbitBreederCategory[]|Collection
     */
    public function getBreederCategories()
    {
        return RabbitBreederCategory::whereNotNull('special')->get()->merge($this->breederCategories);
    }

    /**
     * All ledger categories available for the user
     * @return Category[]|Collection
     */
    public function getLedgerCategories()
    {
        return LedgerCategory::whereNotNull('special')->get()->merge($this->ledgerCategories);
    }

    public function getLedgerStatistics($archived = false, $from = null, $to = null)
    {
        list($totalDebit, $totalCredit) = [0, 0];
        $ledger = $this->ledger();
        if ($archived) {
            $ledger->whereNotNull('archived_at');
        } else {
            $ledger->whereNull('archived_at');
        }
        if ($from != null && $to != null) {
            $ledger->whereBetween('created_at', [$from, $to]);
        }
        foreach ($ledger->get() as $entry) {
            if ($entry->debit) {
                $coef = 1;
                $totalDebit += $entry->amount;
            } else {
                $coef = -1;
                $totalCredit += $entry->amount;
            }
        }
        $breedersCount = $this->breeders()->where('archived', 0)->whereBetween('created_at', [$from, $to])->count();
        return [
            'debits' => $totalDebit,
            'credits' => $totalCredit,
            'debitPerBreeder' => $breedersCount ? round($totalDebit / $breedersCount) : 0,
            'creditPerBreeder' => $breedersCount ? round($totalCredit / $breedersCount) : 0.
        ];
    }

    public function getReasonForDeathStatistics($from='0001-01-01', $to='9999-12-31')
    {
        $reasons_breeders = \DB::table('rabbit_breeders')->select('death_reason')->where('user_id', $this->id)->whereNotNull('death_reason')->whereBetween('created_at', [$from, $to])->pluck('death_reason');
        // $reasons_kits = \DB::table('rabbit_kits')->select('death_reason')->where('user_id', $this->id)->whereNotNull('death_reason')->whereHas('litter', function($query) use ($from, $to) {
        //     $query->whereBetween('born', [$from, $to]);
        // })->pluck('death_reason');
        $reasons_kits = \DB::table('rabbit_kits')->select('death_reason')->where('user_id', $this->id)->whereNotNull('death_reason')->pluck('death_reason');
        $reasons = array_merge($reasons_breeders, $reasons_kits);
        $reasons = array_count_values($reasons);
        array_multisort($reasons, SORT_DESC);
        unset($reasons[0]);
        return $reasons;
    }

    public function getDeathReasonsList($id)
    {
        $reasons= \DB::table('death_reasons')->select('name')->where('user_id', $id)->pluck('name');

        return $reasons;
    }

    public function getPedigreeLogoAttribute($image)
    {
        $temp = true;
        if ($image) {
            $temp = false;
        }

        $imgname = empty($image) ? '' : \Cloudder::show($image, ['height' => '', 'width' => '']);

        return [
            'name'     => $image,
            'path'     => $imgname,
            'temp'     => $temp,
            'oldImage' => $image,
            'delete'   => false,
        ];
    }

    public function getImagesFolder(){
        return 'pedigree';
    }

    /**
     * User is subscribed or still on the initial trial period
     * @return bool
     */
    public function isSubscribed()
    {
        return $this->onGenericTrial() || $this->subscribed() || $this->hasRole(['admin', 'free']);
    }

    /**
     * User is subscribed to premium or on the initial trial period
     * @return bool
     */
    public function isPremium()
    {
        return $this->onGenericTrial() && !$this->subscribed()
                    || $this->subscribedToPlan(Plan::premium()) || $this->hasRole(['admin', 'free']);
    }

    public function isTrial()
    {
        return $this->onGenericTrial() && !$this->subscribed();
    }

    public function getSubscriptionStatusAttribute()
    {
        if (($subscription = $this->subscription()) && $subscription->valid()) {
            return $subscription->stripe_plan;
        }
        if ($this->onGenericTrial()) {
            return 'on trial';
        }
        if (isset($this['trial_ends_at'])) {
            return 'expired';
        }
        return '-';
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    /**
     * @return Transfer[]|Builder
     */
    public function allTransfers()
    {
        return Transfer::where(function (Builder $builder) {
            return $builder->where('user_id', $this->id)
                            ->orWhere('user_email', $this->email);
        });
    }

    /**
     * @param Invoice $invoice
     */
    public function sendInvoice($invoice)
    {
        // @XXX disabled because we opted in for email from stripe
        return;

        $data = [
            'vendor'  => 'Barntrax',
            'product' => 'Hutch',
        ];
        \Mail::send('emails.invoice', compact(['invoice', 'data']), function (Message $message) use ($invoice, $data) {
            $message->to($this->email);
            $message->subject('Invoice ' . $invoice->id);
            $message->attachData($invoice->pdf($data), 'Hutch_' . $invoice->date()->month . '_' . $invoice->date()->year . '.pdf');
        });
    }

    public function getTrialEndsAttribute()
    {
        return $this->trial_ends_at ? $this->trial_ends_at->format(User::getDateFormatPHPSafe()) : null;
    }

    public function setTrialEndsAttribute($ends)
    {
        if ($ends) {
            $this->trial_ends_at = Carbon::createFromFormat(User::getDateFormatPHPSafe(), $ends);
        } else {
            $this->trial_ends_at = null;
        }
    }

    /**
     * @return Plan|null
     */
    public function getPlan()
    {
        $subscription = $this->subscription();
        if (!$subscription || !$subscription->valid()) {
            return null;
        }
        return Plan::find($subscription->stripe_plan);
    }

    public function getMaxBreeders()
    {
        $plan = $this->getPlan();
        return $plan ? $plan->getMaxBreeders() : null;
    }

    public function getMaxArchivedBreeders()
    {
        $plan = $this->getPlan();
        return $plan ? $plan->getMaxArchivedBreeders() : null;
    }

    /**
     * Get the user slug, if user still doesnt have one - assigns it
     * @param bool $save
     * @return string
     */
    public function getSlug($save = true)
    {
        if (!$this->slug) {
            $this->slug = Str::quickRandom(4);
            if ($save) {
            $this->save();
            }
        }
        return $this->slug;
    }

    public function referredBy()
    {
        return $this->belongsTo(User::class, 'referred_by_id');
    }

    public function referrals()
    {
        return $this->hasMany(User::class, 'referred_by_id');
    }

    public function asReferral()
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
        ];
    }

    public function askConfirmReferrer($referrer)
    {
        // This secure hash of this data will make it impossible for user requesting confirmation
        // to fool another user's browser into confirming without the user's intent
        $confirmData = [
            'action' => 'confirm-referrer',
            'referrer' => $referrer->email,
            'referred' => $this->email,
        ];
        \Mail::send('emails.confirm-referrer', [
            'user' => $this,
            'referrer' => $referrer,
            'confirm' => \CryptHash::hash($confirmData),
        ], function (Message $message) {
            $message->to($this->email, $this->name);
            $message->subject('Confirm your referrer on Hutch');
        });
    }

    /**
     * Max amount of $$ this user can be returned for his referrals.
     * @param Customer|null $customer May be skipped
     * @return int
     */
    public function getReferralsCap(Customer $customer = null)
    {
        if ($customer === null) {
            if (!$this->hasStripeId()) {
                return 999;
            }

            $customer = $this->asStripeCustomer();
        }

        $plan = $this->getPlan();
        $coupon = @$customer->discount->coupon;
        $withoutCoupon = 3 * $plan->getAmount() + max(0, $plan->getSetupPrice() - @$coupon->amount_off);

        return $withoutCoupon * (1 - @$coupon->percent_off * .01);
    }

    /**
     * Credits user account (MUST already be a stripe customer), so that the next pay will be reduced
     * @param int $amount
     * @param Customer|null $customer
     * @return int amount actually credited (all amount)
     */
    public function credit($amount, Customer $customer = null)
    {
        if ($customer === null) {
            $customer = $this->asStripeCustomer();
        }

        $customer->account_balance -= $amount;
        $customer->save();

        return $amount;
    }

    /**
     * @param int $amount
     * @param Customer|null $customer
     * @return int amount actually refunded (can be lower then desired)
     */
    public function refund($amount, Customer $customer = null)
    {
        if ($amount <= 0) {
            return 0;
        }

        if ($customer === null) {
            $customer = $this->asStripeCustomer();
        }

        $refunded = 0;
        $charges = $customer->charges();
        /* @var $charges \Stripe\Collection */

        foreach ($charges->autoPagingIterator() as $charge) {
            /* @var $charge Charge */
            $refund = min($amount, $charge->amount - $charge->amount_refunded);
            if ($refund) {
                $charge->refund(['amount' => $refund]);
                $refunded += $refund;
            }
            if ($amount === $refunded) {
                break;
            }
        }

        return $refunded;
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function socialAccounts()
    {
        return $this->hasMany(SocialAccount::class);
    }

    public function mergeInto(User $user)
    {
        if ($this->subscribed()) {
            throw new \Exception('Cannot merge paid account');
        }

        if ($this->id == $user->id) {
            return;
        }

        $user->rawEvents()->sync($this->rawEvents, false);
        $this->rawEvents()->detach();

        $this->ledgerCategories()->update(['user_id' => $user->getKey()]);
        $this->ledger()->update(['user_id' => $user->getKey()]);
        $this->breedChains()->update(['user_id' => $user->getKey()]);
        $this->breeders()->update(['user_id' => $user->getKey()]);
        $this->litters()->update(['user_id' => $user->getKey()]);
        $this->plans()->update(['user_id' => $user->getKey()]);
        $this->rabbitKits()->update(['user_id' => $user->getKey()]);
        $this->notifications()->update(['user_id' => $user->getKey()]);
        $this->breederCategories()->update(['user_id' => $user->getKey()]);
        $this->socialAccounts()->update(['user_id' => $user->getKey()]);
        $this->allTransfers()->update(['user_id' => $user->getKey()]);

        $this->roles()->detach();
        $this->delete();
    }

    /**
     * Updates stripe customer fields (email) for this model.
     * Does nothing if the user is not a stripe customer yet.
     */
    public function updateStripeCustomer()
    {
        if (!$this->hasStripeId()) {
            return;
        }

        $stripe = $this->asStripeCustomer();
        $stripe->email = $this->email;
        $stripe->save();
    }

    /**
     * Walk thought unpaid invoices until we fail to pay
     */
    public function retryInvoices()
    {
        foreach ($this->invoicesIncludingPending() as $invoice) {
            /* @var $invoice Invoice */
            if ($invoice->paid || $invoice->closed || $invoice->forgiven || $invoice->amount_due <= 0) {
                continue;
            }
            $stripeInvoice = $invoice->asStripeInvoice();
            $stripeInvoice->closed = true;
            $stripeInvoice->save();
            $this->invoiceFor('failed invoice ' . $invoice->id, $invoice->amount_due);
        }
    }

    public function getDateFormatPHP() {
        switch ($this->date_format) {
            case 'US':
                return 'm/d/Y';
            case 'INT':
            default:
                return 'd/m/Y';
        }
    }

    public static function getDateFormatPHPSafe()
    {
        if ($user = \Auth::user()) {
            /* @var $user User */
            return $user->getDateFormatPHP();
        } else {
            // Default to US notation
            return 'm/d/Y';
        }
    }

    public function getDateFormatJS() {
        switch ($this->date_format) {
            case 'US':
                return 'MM/DD/YYYY';
            case 'INT':
            default:
                return 'DD/MM/YYYY';
        }
    }

    public function getDateFormatJSShort() {
        switch ($this->date_format) {
            case 'US':
                return 'MM/DD';
            case 'INT':
            default:
                return 'DD/MM';
        }
    }

    public function getDateFormatPhpAttribute() {
        return $this->getDateFormatPHP();
    }

    public function getDateFormatJsAttribute() {
        return $this->getDateFormatJS();
    }

    public function getDateFormatJsShortAttribute() {
        return $this->getDateFormatJSShort();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function pushSubscriptions()
    {
        return $this->hasMany(PushSubscription::class);
    }

    /**
     * The cage card templates that belong to this user.
     */
    public function cageCardTemplates()
    {
        return $this->hasMany(CageCardTemplate::class);
    }
}
