<?php

namespace App\Repositories;


use App\Models\RabbitBreeder;

class RabbitBreederRepository extends Repository
{
    protected $createFromFields = ['prefix', 'name', 'breed', 'cage', 'tattoo', 'sex', 'weight', 'father_id', 'mother_id',
                                   'color', 'aquired', 'date_of_birth', 'image', 'notes', 'legs', 'registration_number',
                                   'champion_number', 'category_id', 'archived', 'died', 'died_at', 'butchered', 'butchered_at', 'sold_at'];
    protected $updateFromFields = ['prefix', 'name', 'breed', 'cage', 'tattoo', 'sex', 'weight', 'father_id', 'mother_id',
                                   'color', 'aquired', 'date_of_birth', 'image', 'notes', 'legs', 'registration_number',
                                   'champion_number', 'category_id'];

    /**
     * RabbitBreederRepository constructor.
     * @param RabbitBreeder $breeder
     */
    public function __construct(RabbitBreeder $breeder)
    {
        $this->object = $breeder;
    }
}
