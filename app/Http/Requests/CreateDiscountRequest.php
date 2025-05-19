<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateDiscountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    public function validationData(): array
    {
        return [
            'name' => $this->input('name'),
            'type' => $this->input('type'),
            'started_at' => $this->input('started_at'),
            'expired_at' => $this->input('expired_at'),
            'usage_limit' => $this->input('usage_limit'),
            'value' => $this->input('value'),
            'trial_days' => $this->input('trial_days'),
            'discount_month' => $this->input('discount_month'),
        ];
    }

    // Nếu có cột discount_month thì mới thêm vào create
    public function rules(): array
    {
        return [
            'name' => 'required|max:255|string',
            'expired_at' => 'nullable|date|after_or_equal:started_at',
            'type' => 'required|in:percentage,amount',
            'value' => $this->percentageValidationRule(),
            'usage_limit' => 'nullable|integer',
            'trial_days' => 'nullable|integer|min:0',
            'discount_month' => 'nullable|numeric|min:0',
        ];
    }

    public function failedValidation(Validator $validator)
    {
        $errors = $validator->errors();
        $errorDetails = [];

        foreach ($errors->messages() as $field => $messages) {
            foreach ($messages as $message) {
                $errorDetails[$field][] = $message;
            }
        }
        throw new HttpResponseException(response()->json([
            'message' => 'Validation failed',
            'errors' => $errorDetails,
        ], 422));
    }

    protected function percentageValidationRule()
    {
        return function ($attribute, $value, $fail) {
            if ($value && ! is_numeric($value)) {
                $fail($attribute . ' must be a number.');

                return;
            }
            $value = (float) $value;
            if ($this->input('type') === 'percentage' && ($value < 0 || $value > 100)) {
                $fail($attribute . ' must be between 0 and 100 when type is percentage.');
            } elseif ($this->input('type') === 'amount' && $value < 0) {
                $fail($attribute . ' must be greater or equal to 0.');
            }
        };
    }
}
