<?php

namespace App\Models\Traits\LikeBreeder;

use App\Models\RabbitBreeder;

trait RabbitKitLikeBreeder
{
    use LikeBreeder;
    
    private function likeBreederPedigree()
    {
        $pedigree_number_generations = $this->user->pedigree_number_generations;
        $this->user->pedigree_number_generations = 1;
        $pedigree = $this->pedigree();
        $this->user->pedigree_number_generations = $pedigree_number_generations;
        return $pedigree['g1'];
    }
    
    public function likeBreeder()
    {
        $pedigree = $this->likeBreederPedigree();
        
        $likeness = new RabbitBreeder();
        foreach (['sex', 'color', 'notes'] as $attribute) {
            $likeness->$attribute = $this->$attribute;
        }
        foreach (['name', 'breed', 'weight', 'aquired']
                 as $attribute) {
            $likeness->$attribute = $pedigree->$attribute;
        }
        
        $likeness->tattoo = $this->given_id;
        $likeness->date_of_birth = $pedigree->day_of_birth;
        $likeness->category_id = 1;
        $likeness->image = $this->image ? $this->image['name'] : null;
        
        return $likeness;
    }
}
