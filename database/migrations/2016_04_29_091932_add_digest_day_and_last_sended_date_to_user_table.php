<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDigestDayAndLastSendedDateToUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_digest_at')->nullable()->after('general_weight_units');
            $table->integer('digest_day')->default(0)->after('general_weight_units');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('digest_day');
            $table->dropColumn('last_digest_at');
        });
    }
}
