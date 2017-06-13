<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUserIdColumnToCageCardTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cage_card_templates', function (Blueprint $table) {
            $table->integer('user_id')->unsigned()->default(4);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
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
            $table->dropForeign('cage_card_templates_user_id_foreign');
            $table->dropColumn('user_id');
        });
    }
}
