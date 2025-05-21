<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CreateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => 'required|max:255|string',
            'type' => 'required|in:percentage,amount',
            'value' => $this->percentageValidationRule(),
            'usage_limit' => 'nullable|integer|min:0',
            'trial_days' => 'nullable|integer|min:0',
            'started_at' => 'nullable|date',
            'expired_at' => 'nullable|date|after_or_equal:started_at',
            'discount_month' => 'nullable|numeric|min:0',
        ];
        return $rules;
    }


    protected function percentageValidationRule()
    {
        return function ($attribute, $value, $fail) {
            if ($value && !is_numeric($value)) {
                $fail($attribute . ' must be a number.');

                return;
            }
            $value = (float)$value;
            if ($this->input('type') === 'percentage' && ($value < 0 || $value > 100)) {
                $fail($attribute . ' must be between 0 and 100 when type is percentage.');
            } elseif ($this->input('type') === 'amount' && $value < 0) {
                $fail($attribute . ' must be greater or equal to 0.');
            }
        };
    }

    public function failedValidation(Validator $validator)
    {
        handleFormRequestValidationFailure($validator);
    }

}
