<?php

namespace App\Http\Requests;

use App\Enum\ExpenseCategory;
use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function rules(): array
    {
        if ($this->isMethod('put')) {
            return [
                'description' => 'sometimes|string',
                'amount' => 'sometimes|numeric',
                'category' => 'sometimes|in:' . implode(
                    ',',
                    array_column(ExpenseCategory::cases(), 'value')
                ),
            ];
        }

        return [
            'description' => 'required|string',
            'amount' => 'required|numeric',
            'category' => 'required|in:' . implode(
                ',',
                array_column(ExpenseCategory::cases(), 'value')
            ),
        ];
    }
}
