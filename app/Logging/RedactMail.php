<?php

namespace App\Logging;

use Monolog\LogRecord;

class RedactMail
{
    public function __invoke(\Illuminate\Log\Logger $logger): void
    {
        $logger->getLogger()->pushProcessor(fn (LogRecord $record) => $record->with(
            message: 'A development email was generated.',
            context: [],
            extra: [],
        ));
    }
}
