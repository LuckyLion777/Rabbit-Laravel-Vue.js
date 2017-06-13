<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TweakPushSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->dropColumn('private_key'); // too dangerous
            $table->renameColumn('public_key', 'server_public_key');
            $table->string('client_public_key')->nullable();
            $table->string('auth_token')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('push_subscriptions', function (Blueprint $table) {
            $table->string('private_key')->nullable();
            $table->renameColumn('server_public_key', 'public_key');
            // I think two other new columns do not have to be deleted (we want to preserve data)
        });
        DB::table('push_subscriptions')->update(['private_key' => config('services.web_push.private')]);
    }
}
