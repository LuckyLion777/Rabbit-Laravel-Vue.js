<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\Purchaseable;
use App\Contracts\Soldable;
use App\Http\Controllers\Controller;
use App\Http\Requests\ClaimRequest;
use App\Models\Traits\LikeBreeder\LikeBreeder;
use App\Models\Transfer;
use Illuminate\Contracts\Auth\Guard;

class TransferController extends Controller
{
    public function breeders(Guard $auth)
    {
        $user = $auth->user();
        /* @var $user \App\Models\User */
        $transfers = $user->allTransfers()->active()->toBreeder()->get();
        return response()->json(['breeders' => $transfers->map(function (Transfer $transfer) {
            $transferable = $transfer->transferable;
            /* @var LikeBreeder $transferable */
            return [
                'transfer_id' => $transfer->getKey(),
            ] + $transferable->likeBreeder()->toArray();
        })]);
    }

    public function transfer(Transfer $transfer, ClaimRequest $request)
    {
        $result = $transfer->claim($request->user());
        $source = $result->getSource(); $target = $result->getTarget();
        if ($source instanceof Soldable 
                && $target instanceof Purchaseable
                && ($soldEntry = $source->soldLedgerSource()->find(false))) {
            $purchasedEntry = $target->purchasedLedgerSource($source->user->email);
            $purchasedEntry->save($soldEntry->amount, false);
        }
        return $request->wantsJson() ? response()->json(['result' => $result->toArray()])
                                        : redirect($result->getUrl());
    }

    // Do not remove $request from the arguments
    public function decline(Transfer $transfer, ClaimRequest $request)
    {
       $transfer->decline();
        return response()->json([]);
    }
}
