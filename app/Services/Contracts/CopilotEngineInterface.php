<?php

namespace App\Services\Contracts;

interface CopilotEngineInterface
{
    public function handle(string $query, array $context = []): string;
}
