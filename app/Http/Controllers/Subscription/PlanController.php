<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Controller;
use App\Models\Subscription\Plan;

class PlanController extends Controller
{
    public function index()
    {
        return response()->json(['plans' => Plan::all()]);
    }
}
