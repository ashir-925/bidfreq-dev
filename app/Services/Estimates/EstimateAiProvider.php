<?php

namespace App\Services\Estimates;

use Illuminate\Container\Attributes\Bind;

#[Bind(GeminiEstimateProvider::class)]
interface EstimateAiProvider
{
    public function analyze(array $input): array;
}
