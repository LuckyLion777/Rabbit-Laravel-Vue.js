<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMissedToPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('breed_plans', function (Blueprint $table) {
            $table->date('missed_date')->nullable()->after('user_id');
            $table->boolean('missed')->default(false)->nullable(false)->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('breed_plans', function (Blueprint $table) {
            $table->dropColumn('missed_date');
            $table->dropColumn('missed');
        });
    }
}
