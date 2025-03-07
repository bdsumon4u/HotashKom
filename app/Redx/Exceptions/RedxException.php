<?php

namespace App\Redx\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Throwable;

class RedxException extends Exception
{
    public function __construct($message = '', $code = 0, public $errors = [], ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Json return
     */
    public function render(): array
    {
        return [
            'error' => true,
            'code' => $this->code,
            'message' => $this->getMessage(),
            'errors' => $this->errors,
        ];
    }

    /**
     * Get validation errors
     *
     * @return JsonResponse
     */
    public function getErrors()
    {
        return response()->json($this->errors, $this->code);
    }
}
