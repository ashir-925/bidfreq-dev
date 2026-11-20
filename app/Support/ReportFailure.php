<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use PDOException;
use Throwable;

class ReportFailure
{
    public static function log(Throwable $exception, string $action, ?int $userId = null): void
    {
        $request = request();
        // Raw exception text can contain SQL bindings, transport credentials or reset links.
        Log::channel('daily')->error('BidFreq operation failed', [
            'action' => $action,
            'route' => $request->route()?->getName(),
            'user_id' => $userId ?? (Auth::hasUser() ? Auth::id() : null),
            'exception' => $exception::class,
            'message' => $exception instanceof PDOException
                ? 'Database error (SQLSTATE '.$exception->getCode().').' : 'The operation raised an internal exception.',
        ]);
    }
}
