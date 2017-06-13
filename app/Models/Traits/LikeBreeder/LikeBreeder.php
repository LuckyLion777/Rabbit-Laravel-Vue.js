<?php

namespace App\Models\Traits\LikeBreeder;

use App\Models\RabbitBreeder;

/**
 * Trait for entities which can construct their own likeness as a new breeder
 * @package App\Models\Traits\LikeBreeder
 */
trait LikeBreeder
{
    /**
     * @return RabbitBreeder
     */
    abstract public function likeBreeder();
}
