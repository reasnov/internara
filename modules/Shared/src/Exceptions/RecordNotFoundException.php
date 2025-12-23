<?php

declare(strict_types=1);

namespace Modules\Shared\Exceptions;

use Throwable;

/**
 * Class RecordNotFoundException.
 *
 * A specialized exception for scenarios where a requested data record
 * or resource cannot be located in the system.
 */
class RecordNotFoundException extends AppException
{
    /**
     * Create a new RecordNotFoundException instance.
     *
     * @param  string  $userMessage  The user-friendly message (e.g., "The requested post could not be found.").
     * @param  int  $code  The HTTP status code, defaulting to 404 (Not Found).
     * @param  Throwable|null  $previous  The previous exception used for chaining.
     * @param  array  $record  The record identifier(s) that were not found, for logging context.
     */
    public function __construct(
        string $userMessage,
        int $code = 404,
        ?Throwable $previous = null,
        array $record = []
    ) {
        $context = [];

        if (! empty($record)) {
            $context['record'] = $record;
        }

        parent::__construct(
            userMessage: $userMessage,
            logMessage: 'Record not found.',
            code: $code,
            previous: $previous,
            context: $context
        );
    }
}
