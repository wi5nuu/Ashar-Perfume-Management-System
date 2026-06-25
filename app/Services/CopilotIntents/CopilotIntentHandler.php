<?php

namespace App\Services\CopilotIntents;

interface CopilotIntentHandler
{
    public function handle(): string;
}
