<?php

return [
    'standard' => [
        'semi_member' => [
            'regular' => [
                'bare' => ['INR' => 14775, 'EUR' => 287],
                'shell' => ['INR' => 16475, 'EUR' => 325],
            ],
            'early_bird' => [
                'bare' => ['INR' => 12250, 'EUR' => 250],
                'shell' => ['INR' => 13450, 'EUR' => 286],
            ],
        ],
        'non_semi_member' => [
            'regular' => [
                'bare' => ['INR' => 19450, 'EUR' => 376],
                'shell' => ['INR' => 21675, 'EUR' => 426],
            ],
            'early_bird' => [
                'bare' => ['INR' => 16125, 'EUR' => 328],
                'shell' => ['INR' => 17700, 'EUR' => 375],
            ],
        ],
    ],
    'premium' => [
        'semi_member' => [
            'regular' => [
                'bare' => ['INR' => 15500, 'EUR' => 301],
                'shell' => ['INR' => 17300, 'EUR' => 341],
            ],
            'early_bird' => [
                'bare' => ['INR' => 12875, 'EUR' => 262],
                'shell' => ['INR' => 14125, 'EUR' => 300],
            ],
        ],
        'non_semi_member' => [
            'regular' => [
                'bare' => ['INR' => 20400, 'EUR' => 394],
                'shell' => ['INR' => 22775, 'EUR' => 447],
            ],
            'early_bird' => [
                'bare' => ['INR' => 16950, 'EUR' => 343],
                'shell' => ['INR' => 18575, 'EUR' => 393],
            ],
        ],
    ],
    'gst_rate' => 0.18, // 18%
    'processing_charge_rate' => 0.03, // 3%
];
