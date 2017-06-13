<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Auth\Guard;
use Laravel\Cashier\Invoice;

class InvoiceController extends Controller
{
    public function index(Guard $auth)
    {
        return response()->json(['invoices' => $auth->user()->invoices()->map(function (Invoice $invoice) use ($auth) {
                return [
                    'id' => $invoice->asStripeInvoice()->id,
                    'date' => $invoice->date()->format($auth->user()->getDateFormatPHP()),
                    'total' => $invoice->total(),
                ];
            })
        ]);
    }

    public function show($invoice, Guard $auth)
    {
        $user = $auth->user();
        /* @var $user \App\Models\User */
        return $user->downloadInvoice($invoice, [
            'vendor'  => 'Barntrax',
            'product' => 'Hutch',
        ]);
    }
}
