<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

abstract class BusinessRuleException extends Exception
{
    abstract protected function field(): string;

    public function render(): JsonResponse
    {
        return response()->json([
            'message' => $this->getMessage(),
            'errors' => [$this->field() => [$this->getMessage()]],
        ], Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
