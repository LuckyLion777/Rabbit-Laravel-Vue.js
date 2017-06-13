<?php

namespace App\Providers;

use App\Contracts\CryptHash as CryptHashContract;
use App\Contracts\DeviceTracker as DeviceTrackerContract;
use App\Contracts\ImportExport\CsvExporter as CsvExporterContract;
use App\Contracts\ImportExport\CsvImporter as CsvImporterContract;
use App\Contracts\ImportExport\EvansImporter as EvansImporterContract;
use App\ImportExport\CsvExporter;
use App\ImportExport\CsvImporter;
use App\ImportExport\EvansImporter;
use App\Models\Event;
use App\Models\Ledger\Category;
use App\Models\Litter;
use App\Models\RabbitBreeder;
use App\Models\RabbitBreederCategory;
use App\Models\Subscription\Plan;
use App\Models\User;
use App\Pdf\Contracts\Mpdf as MpdfContract;
use App\Pdf\Contracts\MpdfInstance as MpdfInstanceContract;
use App\Pdf\Mpdf;
use App\Pdf\MpdfInstance;
use App\Push\Contracts\Pusher as PusherContract;
use App\Push\Pusher as CompositePusher;
use App\Push\Web\Pusher;
use App\Security\CryptHash;
use App\Security\DeviceTracker;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Validator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(Guard $auth)
    {
        if (config('app.env') === 'production') {
            error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        }
//        RabbitKit::updated(function ($kit) {
//            if (array_key_exists('weight', $kit->getDirty())) {
//                event(new KitWasWeighed($kit, 'rabbit'));
//            }
//        });

        Event::deleting(function ($event) {
            $event->{$event->type . 's'}()->detach();
            if ($event->type != 'general')
                $event->generals()->detach();
        });

        Relation::morphMap([
            Category::CATEGORY_BREEDER => RabbitBreeder::class,
            Category::CATEGORY_LITTER => Litter::class,
        ]);

        \Validator::extend('breeder_category_exists', function ($attribute, $value, $parameters, $validator) use ($auth) {
            return RabbitBreederCategory::where('id', $value)->where(function(Builder $builder) use ($auth) {
                $builder->where('user_id', $auth->user()->id)->orWhereNotNull('special');
            })->exists();
        });
        \Validator::extend('breeder_category_unique', function ($attribute, $value, $parameters, $validator) use ($auth) {
            $categories = RabbitBreederCategory::where('name', $value)->where(function(Builder $builder) use ($auth) {
                $builder->where('user_id', $auth->user()->id)->orWhereNotNull('special');
            });
            if (count($parameters) >= 1) {
                $categories->where('id', '!=', $parameters[0]);
            }
            return !$categories->exists();
        });
        \Validator::extend('ledger_category_exists', function ($attribute, $value, $parameters, $validator) use ($auth) {
            return Category::where('id', $value)->where(function(Builder $builder) use ($auth) {
                $builder->where('user_id', $auth->user()->id)->orWhereNotNull('special');
            })->exists();
        });
        \Validator::extend('ledger_category_unique', function ($attribute, $value, $parameters, $validator) use ($auth) {
            $categories = Category::where('name', $value)->where(function(Builder $builder) use ($auth) {
                $builder->where('user_id', $auth->user()->id)->orWhereNotNull('special');
            });
            if (count($parameters) >= 1) {
                $categories->where('id', '!=', $parameters[0]);
            }
            return !$categories->exists();
        });
        \Validator::extendImplicit('ledger_association', function ($attribute, $value, $parameters, $validator) {
            $category = Category::find(array_get($validator->getData(), 'category_id'));
            if (!$category) return !$value;
            switch ($category->special) {
                case 'breeder':
                    $breeder = RabbitBreeder::find($value);
                    return $breeder && $breeder->user_id == auth()->id();
                case 'litter':
                    $litter = Litter::find($value);
                    return $litter && $litter->user_id == auth()->id();
                default:
                    return !$value;
            }
        });
        \Validator::extend('not_user_email', function ($attribute, $value, $parameters, $validator) use ($auth) {
            return $auth->user()->email !== $value;
        });
        \Validator::extend('valid_plan', function($attribute, $value, $parameters, $validator) {
            return !!Plan::find($value);
        });
        \Validator::extend('not_current_plan', function($attribute, $value, $parameters, $validator) use ($auth) {
            $user = $auth->user();
            /* @var $user User */
            return !$user->subscribedToPlan($value);
        });
        \Validator::extendImplicit('required_unless_subscribed', function($attribute, $value) use ($auth) {
            if ($value != null && $value !== '') {
                return true;
            }

            $user = $auth->user();
            /* @var $user User */
            return $user->isSubscribed();
        });
        \Validator::extend('referrer', function ($attribute, $value, $parameters, $validator) use ($auth) {
            // If first parameter is set we consider is as a final confirmation
            // which can be launched from any user or even without authenticating
            if (!@$parameters[0]) {

                $user = $auth->user();
                /* @var $user User */
                if ($user->email === $value) {
                    $GLOBALS['referrer_error_message'] = 'This is your email.';
                    return false;
                }
            }

            $referrer = User::where('email', $value)->first();
            /* @var $referrer User */
            if (!$referrer) {
                $GLOBALS['referrer_error_message'] = 'There is no registered users with this email.';
                return false;
            }

            if (!$referrer->subscribed()) {
                $GLOBALS['referrer_error_message'] = 'This user does not have active subscriptions.';
                return false;
            }

            return true;
        });
        \Validator::replacer('referrer', function () {
            return $GLOBALS['referrer_error_message'];
        });
        \Validator::extend('referred', function ($attribute, $value, $parameters, $validator) use ($auth) {
            // If first parameter is set we consider is as a final confirmation

            $user = $auth->user();
            /* @var $user User */

            if (!@$parameters[0]) {
                if ($user->email === $value) {
                    $GLOBALS['referral_error_message'] = 'This is your email.';
                    return false;
                }
            } else {
                if ($user && $user->email !== $value) {
                    $GLOBALS['referral_error_message'] = 'This confirmation was issued for another user.';
                    return false;
                }
            }

            $referral = User::where('email', $value)->first();
            /* @var $referral User */
            if (!$referral) {
                $GLOBALS['referral_error_message'] = 'There is no registered users with this email.';
                return false;
            }

            if ($referral->referredBy) {
                $GLOBALS['referral_error_message'] = 'This user already specified his referrer.';
                return false;
            }

            if (!$referral->subscribed()) {
                $GLOBALS['referral_error_message'] = 'This user does not have active subscriptions.';
                return false;
            }

            return true;
        });
        \Validator::replacer('referred', function () {
            return $GLOBALS['referral_error_message'];
        });
        \Validator::extend('referrer_confirm', function ($attribute, $value, $parameters, $validator) use ($auth) {
            /* @var $validator Validator */
            $confirmData = [
                'action' => 'confirm-referrer',
                'referrer' => $validator->getData()['email'],
                'referred' => $validator->getData()['me'],
            ];
            return \CryptHash::check($confirmData, $value);
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(MpdfContract::class, Mpdf::class);
        $this->app->bind(MpdfInstanceContract::class, MpdfInstance::class);
        $this->app->singleton(CryptHashContract::class, CryptHash::class);

        $this->app->bind(CsvExporterContract::class, CsvExporter::class);
        $this->app->bind(CsvImporterContract::class, CsvImporter::class);
        $this->app->bind(EvansImporterContract::class, EvansImporter::class);

        $this->app->singleton(Pusher::class, function () {
            return new Pusher(['TTL' => 3600 * 24]);
        });

        $this->app->singleton(PusherContract::class, function () {
            return new CompositePusher([
                // Add all the pushers available here
                app(Pusher::class)
            ]);
        });
        $this->app->singleton(DeviceTrackerContract::class, DeviceTracker::class);
    }
}
