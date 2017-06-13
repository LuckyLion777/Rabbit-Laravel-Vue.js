<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class ResetTrialTo7Days extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('users')
            // only update for users without trial or with too much of a trial currently
            ->where(function (\Illuminate\Database\Query\Builder $query) {
                $query->whereNull('trial_ends_at')->orWhere('trial_ends_at', '>', Carbon::now()->addDays(7));
            })
            ->update(['trial_ends_at' => Carbon::now()->addDays(7)]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // do nothing
    }
}
