<?php

return [
    'table' => 'chronicles',
    'user_class' => \App\Models\User::class,
    'eloquent_observed_classes' => [
        // Add the classes you want observed here
        \App\Models\Litter::class
    ],
];
