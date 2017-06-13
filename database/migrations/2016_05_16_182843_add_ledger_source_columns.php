<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLedgerSourceColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->string('source_event', 70)->nullable()->default(null);
            $table->unsignedInteger('source_id')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->dropColumn('source_id');
            $table->dropColumn('source_event');
        });
    }
}
