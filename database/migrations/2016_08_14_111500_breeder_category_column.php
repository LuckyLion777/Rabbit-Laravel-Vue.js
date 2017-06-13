<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BreederCategoryColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->nullable(true)->default(null);
        });
        DB::table('rabbit_breeders')->update([
            'category_id' => DB::table('rabbit_breeder_categories')->where('default', true)->first(['id'])->id
        ]);
        Schema::table('rabbit_breeders', function (Blueprint $table) {
            $table->unsignedInteger('category_id')->nullable(false)->change();
            $table->foreign('category_id')->references('id')->on('rabbit_breeder_categories');
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
            $table->dropColumn('category_id');
        });
    }
}
