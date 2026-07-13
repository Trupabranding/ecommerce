<?php

return [
    'timezone' => env('DEFAULT_TIMEZONE', 'Asia/Manila'),
    'currency' => env('DEFAULT_CURRENCY', 'USD'),
    'currency_symbol' => env('DEFAULT_CURRENCY_SYMBOL', '$'),
    'date_time_display_format' =>'M d, Y h:i A',
    'branch_feature_enabled' => env('BRANCH_FEATURE_ENABLED', false),
    'branch_feature_license_hash' => env('BRANCH_FEATURE_LICENSE_HASH'),
    'branch_feature_license_key' => env('BRANCH_FEATURE_LICENSE_KEY'),
];
