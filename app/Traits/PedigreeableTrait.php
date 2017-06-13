<?php

namespace App\Traits;

use App\Models\Pedigree;
use App\Models\RabbitBreeder;
use App\Models\RabbitKit;

trait PedigreeableTrait
{
    private function newPedigree($level, $sex, $pedigree = false)
    {
        // ugly hack. if you're feeling like refactorying:
        // * create interface with method like getPedigreeOwnerField
        // * implement this interface in every class with this trait
        switch (get_class()) {
            case RabbitBreeder::class:
                $owner_field = 'rabbit_breeder_id';
                break;
            case RabbitKit::class:
                $owner_field = 'rabbit_kit_id';
                break;
            default:
                throw new \LogicException('pedigree owner field is not defined for ' . get_class());
        }

        // Ok, so that's a little complicated. What we need is to establish parental chain from this kit to the pedigree record
        // For example, for g4.f1 it is $this->father->father->father, for g4.m4 $this->mother->mother->mother
        // I watch bits in the generation number for the beginning of the chain, and the required entry's sex for the last element

        list($gen, $type) = explode('.', $level); $gen = (int) $gen[1];

        $mask = decbin($type[1] - 1);
        $mask = str_pad($mask, $gen - 1 - strlen($mask), '0', STR_PAD_LEFT);

        $current = $this; $parents = [];
        for ($i = 2; $i < $gen; $i++) {
            // echo ($i - 2) ." " . $mask[$i - 2] . "<br>";
            // print_r($current);
            $current = $mask[$i - 2] ? $current->mother : $current->father;
            if (!$current) break;
            $parents[] = $current;
        }

        if ($current) {
            $current = $type[0] === 'f' ? $current->father : $current->mother;
        }
//        if ($level == 'g3.m1') die (print_r(compact('mask', 'gen', 'type', 'level', 'current')));

        // Here, we made it!

        if (!$pedigree) $pedigree = new Pedigree();
        $pedigree->rabbit_kit_id = null;
        $pedigree->rabbit_breeder_id = null;
        $pedigree->$owner_field = $this->id;
        $pedigree->level = $level;
        $pedigree->sex = $sex;
        // if($level == 'g3.f1')
        // {
        //     dd($current, $pedigree);
        // }
        if (!$pedigree->rabbit_breeders_id) {
            //$pedigree->rabbit_breeders_id = null;
        //}

            if ($current) {
				$pedigree->prefix = $current->prefix;
				$pedigree->name = $current->name;
                $pedigree->notes = $current->notes;
                $pedigree->custom_id = $current->tattoo;
                $pedigree->day_of_birth = $current->date_of_birth;
                $pedigree->aquired = $current->aquired;
                $pedigree->color = $current->color;
                $pedigree->weight = $current->weight;
                //$pedigree->notes = $current->notes;
                $pedigree->breed = $current->breed;

                $pedigree->image = $current->image['name'];
                $pedigree->rabbit_breeders_id = $current->id;

                $pedigree->legs = $current->legs;
                $pedigree->registration_number = $current->registration_number;
                $pedigree->champion_number = $current->champion_number;
            } else {
                // some moar bit magic
                $code = 2 * ($type[1] - 1) + ($type[0] === 'm');
                foreach ($parents as $depth => $parent) {
                    // calculate pedigree cell level relative to the parent
                    $bits = $gen - $depth - 2;
                    $relCode = $code & ((1 << $bits) - 1);
                    $relSex = $relCode % 2 ? 'm' : 'f';
                    $relNum = floor($relCode / 2) + 1;
                    $relType = $relSex . $relNum;
                    $relGen = 'g' . ($gen - $depth - 1);
                    $relLevel = $relGen . '.' . $relType;

                    $record = $parent->pedigrees()->where('level', $relLevel)->first();
                    if ($record) {
                        foreach (['prefix','name', 'custom_id', 'day_of_birth', 'aquired', 'color', 'weight',
                                     'notes', 'breed', 'legs', 'registration_number', 'champion_number'] as $field) {
                            if (!$pedigree->$field && $record->$field) {
                                $pedigree->$field = $record->$field;
                            }
                        }
                        if ((!$pedigree->image || !$pedigree->image['name'])
                            && ($record->image && $record->image['name'])) {
                            $pedigree->image = $record->image['name'];
                        }
                    }
                }
            }
        }
        $pedigree->save();
        return $pedigree;
    }

    public function getFirstGen()
    {
        $current = $this;
        $pedigree = Pedigree::where('rabbit_breeder_id', $current->id)->where('level', 'me')->first();
        // dd((!$pedigree));
        if (!$pedigree) {
            $pedigree = new Pedigree();
            // dd($pedigree);
            $pedigree->rabbit_kit_id = null;
            $pedigree->rabbit_breeder_id = null;
            $pedigree->rabbit_breeder_id = $this->id;
            $pedigree->level = 'me';
            $pedigree->sex = $current->sex;
            $pedigree->prefix = $current->prefix;
            $pedigree->name = $current->name;
            $pedigree->notes = $current->notes;
            $pedigree->custom_id = $current->tattoo;
            $pedigree->day_of_birth = $current->date_of_birth;
            $pedigree->aquired = $current->aquired;
            $pedigree->color = $current->color;
            $pedigree->weight = $current->weight;
            //$pedigree->notes = $current->notes;
            $pedigree->breed = $current->breed;

            $pedigree->image = $current->image['name'];
            $pedigree->rabbit_breeders_id = $current->id;

            $pedigree->legs = $current->legs;
            $pedigree->registration_number = $current->registration_number;
            $pedigree->champion_number = $current->champion_number;
            $pedigree->save();
        }
        return $pedigree;
    }
}
