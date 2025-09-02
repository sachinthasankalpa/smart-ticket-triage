<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TicketStatus;
use App\Enums\TicketCategory;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', Rule::in(TicketStatus::values())],
            'category' => ['sometimes', Rule::in(TicketCategory::values())],
            'internal_note' => 'nullable|string',
        ];
    }
}
