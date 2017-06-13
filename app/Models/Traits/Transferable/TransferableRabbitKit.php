<?php

namespace App\Models\Traits\Transferable;

use App\Models\Pedigree;
use App\Models\RabbitBreeder;
use App\Models\Traits\LikeBreeder\RabbitKitLikeBreeder;
use App\Models\Transfer\TransferResult;
use App\Models\User;

trait TransferableRabbitKit
{
    use Transferable, RabbitKitLikeBreeder;


    public function transfer(User $user)
    {
        $transferred = $this->likeBreeder();
        $user->breeders()->save($transferred);
        
        $pedigree = $this->transferablePedigree();

        foreach (array_slice($pedigree, 1) as $level => $pedigreeGeneration) {
            foreach ($pedigreeGeneration as $pedigreeRecord) {
                $pedigreeCopy = new Pedigree();
                foreach (['level', 'name', 'custom_id', 'day_of_birth', 'aquired', 'color', 'weight', 'breed',
                             'sex', 'notes']
                         as $attribute) {
                    $pedigreeCopy->$attribute = $pedigreeRecord->$attribute;
                }

                $pedigreeCopy->level = $pedigreeRecord->level;
                $pedigreeCopy->name = $pedigreeRecord->name;
                $pedigreeCopy->custom_id = $pedigreeRecord->custom_id;
                $pedigreeCopy->day_of_birth = $pedigreeRecord->day_of_birth;
                $pedigreeCopy->aquired = $pedigreeRecord->aquired;
                $pedigreeCopy->image = $pedigreeRecord->image ? $pedigreeRecord->image['name'] : null;

                $transferred->pedigrees()->save($pedigreeCopy);
            }
        }

        return new TransferResult($this, $transferred, '/#!/profile/' . $transferred->getKey());
    }

    protected function getType()
    {
        return 'kit';
    }

    protected function getTypePlural()
    {
        return 'kits';
    }
}
