<?php

namespace App\Http\Requests;

use App\Services\ProductService;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class IndexProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer', 'min:1', 'max:100'],
        ];
    }

    public function search(): ?string
    {
        return $this->validated('search');
    }

    public function perPage(): int
    {
        return (int) $this->validated('per_page', ProductService::DEFAULT_PER_PAGE);
    }
}
