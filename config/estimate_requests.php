<?php

return [
    'gemini_key' => env('GEMINI_API_KEY'),
    'gemini_model' => env('GEMINI_MODEL', 'gemini-3.8-flash'),
    'gemini_fallback_model' => env('GEMINI_FALLBACK_MODEL', 'gemini-3.1-flash-lite'),
    'max_attempts' => (int) env('AI_MAX_ATTEMPTS', 5),
    'queue_connection' => 'database',
    'disk' => 'local',
];
