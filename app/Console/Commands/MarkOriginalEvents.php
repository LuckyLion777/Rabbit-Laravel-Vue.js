<?php

namespace App\Console\Commands;

use App\Models\BreedChain;
use Illuminate\Console\Command;

class MarkOriginalEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mark-original-events';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $colorBreed = 'fa-venus-mars bg-blue';
        $colorBreedOriginal = 'fa-venus-mars bg-blue original';
        $colorBirth = 'fa-birthday-cake bg-green';
        $colorBirthOriginal = 'fa-birthday-cake bg-green original';

        $nameBreed = 'breed';
        $nameBirth = 'kindle/birth';

        $q = <<<SQL
UPDATE `breed_chains`
INNER JOIN (
  SELECT *
  FROM `breed_chains`
  WHERE
    `icon` IN (
      -- I also include already fixed (appended with 'original') records here so that they will prevent
      -- another record for the same user from being updated
      :color_breed, :color_breed_original_1,
      :color_birth, :color_birth_original_1
    )
  GROUP BY `user_id`
  HAVING `icon` NOT IN (:color_breed_original_3, :color_birth_original_3)
  ORDER BY
    (`icon` = :color_breed_original_2) DESC, (`icon` = :color_birth_original_2) DESC,
    -- Let's try to find original event by their original names. Of cause, user could have changed that
    (`name` = :name_breed) DESC, (`name` = :name_birth) DESC, `days` ASC
) AS `filtered`
  ON `breed_chains`.`id` = `filtered`.`id`
SET
  `breed_chains`.`icon` = CONCAT(`breed_chains`.`icon`, :original_suffix)
SQL;
        \DB::update($q, [
            ':color_breed' => $colorBreed, ':color_birth' => $colorBirth,
            ':color_breed_original_1' => $colorBreedOriginal, ':color_birth_original_1' => $colorBirthOriginal,
            ':color_breed_original_2' => $colorBreedOriginal, ':color_birth_original_2' => $colorBirthOriginal,
            ':color_breed_original_3' => $colorBreedOriginal, ':color_birth_original_3' => $colorBirthOriginal,
            ':name_breed' => $nameBreed, ':name_birth' => $nameBirth, ':original_suffix' => ' original'
        ]);
    }
}
