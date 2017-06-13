<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class IncreaseFieldsLengthForCageCardTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cage_card_templates', function (Blueprint $table) {
            $table->string('fields', 512)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cage_card_templates', function (Blueprint $table) {
            $table->string('fields', 256)->change();
        });
    }
}
