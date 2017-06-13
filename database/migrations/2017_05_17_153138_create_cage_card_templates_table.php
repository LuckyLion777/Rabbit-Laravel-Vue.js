<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateCageCardTemplatesTable
 */
class CreateCageCardTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cage_card_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->string('size');
            $table->string('hole');
            $table->string('fields');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('cage_card_templates');
    }

    /**
     * Get template name.
     */
    public function getTemplateNameAttribute()
    {
        return '';
    }

    /**
     * Get breeders fields options.
     */
    public static function getBreedersFieldsOptions()
    {

    }

    /**
     * Get breeders fields options.
     */
    public static function getLittersFieldsOptions()
    {

    }
}
