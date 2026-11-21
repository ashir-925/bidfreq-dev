<?php

return [
    'provider' => env('PROJECT_PAYMENT_PROVIDER', 'stripe'),
    'stripe' => [
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),

    ],
];

