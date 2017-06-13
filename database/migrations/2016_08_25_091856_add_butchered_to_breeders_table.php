<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddButcheredToBreedersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->timestamp('butchered_at')->nullable()->after('archived');
            $table->boolean('butchered')->default(false)->after('archived');

            $table->timestamp('died_at')->nullable()->after('archived');
            $table->boolean('died')->default(false)->after('archived');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->dropColumn('butchered');
            $table->dropColumn('butchered_at');
            $table->dropColumn('died');
            $table->dropColumn('died_at');
        });
    }
}
