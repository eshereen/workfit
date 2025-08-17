<?php
return [
    'rules' => [
        'purchase' => [
            'points' => 1,
            'description' => 'Earned from purchase',
        ],
        'signup' => [
            'points' => 100,
            'description' => 'Signup bonus',
        ],
        'review' => [
            'points' => 20,
            'description' => 'Product review',
        ],
    ],
    'redemption' => [
        'ratio' => 100,
    ],
];
