<?php

namespace App\Http\Controllers;

use App\Http\Requests\AskEmailRequest;
use App\Models\SocialAccount;

class WizardController extends Controller
{
    public function askEmail(AskEmailRequest $request)
    {
        if (!$request->session()->has("auth-social-{$request->query('social')}")) {
            abort(403, 'Forbidden');
        }

        $social = SocialAccount::findOrFail($request->query('social'));
        if ($social->user) {
            abort(403, 'Forbidden');
        }

        /* @var $social SocialAccount */
        return view('wizard', ['social' => $social]);
    }
}
