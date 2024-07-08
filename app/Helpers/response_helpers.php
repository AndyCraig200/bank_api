<?php
use App\Exceptions\JsonAbortException;
if (!function_exists('abort_with_json')) {
    /**
     * Abort the request and return a JSON response.
     *
     * @param int $statusCode
     * @param string $message
     * @return void
     */
    function abort_with_json(int $statusCode, string $message): void
    {
        throw new JsonAbortException($message, $statusCode);
    }
}
