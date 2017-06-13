<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetDefaultForWeightSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function ($table) {
            $table->string('general_weight_units')->default('Pounds')->change();
        });
        $users = \App\Models\User::where('general_weight_units', '')->get();

        foreach ($users as $user) {
            echo $user->id . "\n";
            $user->general_weight_units = 'Pounds';
            $user->save();
        }

        echo $users->count();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->string('general_weight_units')->default()->change();
        });
    }
}
