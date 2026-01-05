<?php

declare(strict_types=1);

namespace Modules\Shared\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Throwable;

/**
 * Class AppException.
 *
 * This is the foundational custom exception class for all domain-specific or
 * business logic errors within the application. It provides a robust mechanism
 * to separate user-friendly messages from internal logging details and
 * allows for attaching contextual information.
 */
class AppException extends Exception
{
    protected string $userMessage;

    protected string $logMessage;

    /**
     * Create a new exception instance.
     *
     * @param  string  $userMessage  The translation key for the user-friendly message (e.g., "user::exceptions.not_found").
     * @param  array  $replace  Parameters to pass to the translator for replacement (e.g., ['name' => 'John']).
     * @param  string|null  $locale  Specific locale to use for the user message, or null for default.
     * @param  string|null  $logMessage  The technical message for logging (optional, defaults to $userMessage).
     * @param  int  $code  The HTTP status code or internal error code (default 422 - Unprocessable Content).
     * @param  Throwable|null  $previous  The previous exception used for chaining (optional).
     * @param  array  $context  Additional context data to be logged with the exception.
     */
    public function __construct(
        string $userMessage,
        protected array $replace = [],
        protected ?string $locale = null,
        ?string $logMessage = null,
        int $code = 422,
        ?Throwable $previous = null,
        protected array $context = []
    ) {
        $this->userMessage = trim($userMessage);
        $this->logMessage = trim($logMessage ?? $this->userMessage);

        parent::__construct($this->logMessage, $code, $previous);
    }

    /**
     * Get the translated, user-friendly message.
     *
     * This message is intended for display to the end-user.
     * It uses the userMessage property as a translation key.
     *
     * @return string The translated, user-friendly message.
     */
    public function getUserMessage(): string
    {
        return __($this->userMessage, $this->replace, $this->locale);
    }



    /**
     * Get the technical log message.
     *
     * This message is intended for internal logging and debugging purposes,
     * providing more technical details about the error.
     *
     * @return string|null The technical log message.
     */
    public function getLogMessage(): ?string
    {
        return $this->logMessage;
    }

    /**
     * Get additional context data for the exception.
     *
     * @return array The context data.
     */
    public function getContext(): array
    {
        return $this->context;
    }

    /**
     * Get a subset of the exception trace stack.
     *
     * @param  int  $limit  The maximum number of trace stacks to include.
     * @return array The trace stacks.
     */
    public function getSubTrace(int $limit = 6): array
    {
        return \array_slice($this->getTrace(), 0, $limit);
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse|RedirectResponse
    {
        if ($request->expectsJson()) {
            $payload = [
                'message' => $this->getUserMessage(),
            ];

            if (config('app.debug')) {
                $payload['context'] = $this->getContext();
                $payload['stack'] = $this->getSubTrace();
            }

            return response()->json($payload, $this->getCode() ?: 422);
        }

        return redirect()
            ->back()
            ->withInput($request->input())
            ->with('error', $this->getUserMessage());
    }
}
