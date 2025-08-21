<?php
return [
    'rules' => [
        'purchase' => [
            'points_per_dollar' => 1, // 1 point per $1
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
        'ratio' => 100, // 100 points = $1
        'minimum_redemption' => 100, // Minimum points to redeem
    ],
];
