<?php

namespace App\Http\Requests;

use App\Models\Transfer;

class ClaimRequest extends Request
{
    private $unauthorizedMessage;

    /**
     * @return Transfer
     */
    private function getTransfer()
    {
        return $this->route('transfer');
    }

    public function authorize()
    {
        $user = $this->getTransfer()->user;
        if (!$this->user()) {
            $this->session()->set('inviter', $this->getTransfer()->transferable->user->id);
            return false;
        }
        if ($user && $user->id != $this->user()->id
                || !$user && strtolower($this->getTransfer()->user_email) != strtolower($this->user()->email)) {
            $this->unauthorizedMessage = 'This transfer is for another user.';
            return false;
        }

        if ($this->getTransfer()->resolved_at) {
            $this->unauthorizedMessage = 'This transfer operation has already completed.';
            return false;
        }

        return true;
    }

    public function rules()
    {
        return [];
    }

    public function failedAuthorization()
    {
        if ($this->unauthorizedMessage) {
            $this->session()->set('callout_error', $this->unauthorizedMessage);
        }
        parent::failedAuthorization();
    }

    public function forbiddenResponse()
    {
        if ($this->wantsJson()) {
            return parent::forbiddenResponse();
        }
        if (!$this->user() && !$this->getTransfer()->user) {
            return response()->redirectTo('/register')->withInput(['email' => $this->getTransfer()->user_email]);
        }
        return response()->redirectTo('/');
    }
}
