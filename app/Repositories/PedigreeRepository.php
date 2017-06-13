<?php

namespace App\Repositories;


use App\Models\Pedigree;

class PedigreeRepository extends Repository
{
    protected $createFromFields = ['prefix', 'name', 'custom_id', 'day_of_birth', 'breed', 'sex', 'image', 'notes', 'aquired', 'color', 'weight', 'legs', 'registration_number', 'champion_number'];
    protected $updateFromFields = ['prefix', 'name', 'custom_id', 'day_of_birth', 'breed', 'sex', 'image', 'notes', 'aquired', 'color', 'weight', 'legs', 'registration_number', 'champion_number'];

    /**
     * PedigreeRepository constructor.
     * @param Pedigree $pedigree
     */
    public function __construct(Pedigree $pedigree)
    {
        $this->object = $pedigree;
    }
}
