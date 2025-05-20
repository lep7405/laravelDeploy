<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class UpdateDiscountRequest extends FormRequest
{
    /**
     * Cache for coupon usage check
     */
    private $hasUsedCoupons = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Check if discount has any used coupons
     */
    private function hasUsedCoupons(): bool
    {
        if ($this->hasUsedCoupons === null) {
            $discountRepo = app()->make('App\Repositories\Discount\DiscountRepository');
            $discount = $discountRepo->find($this->route('id'));

            $this->hasUsedCoupons = $discount->coupon->contains(function ($item) {
                return $item->times_used > 0;
            });
        }

        return $this->hasUsedCoupons;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $baseRules = [
            'name' => 'required|string|max:255',
            'started_at' => 'nullable|date',
            'expired_at' => 'nullable|date|after_or_equal:started_at',
        ];

        if (!$this->hasUsedCoupons()) {
            return array_merge($baseRules, [
                'value' => 'required|numeric|min:0',
                'usage_limit' => 'nullable|integer|min:0',
                'trial_days' => 'nullable|integer|min:0',
                'discount_month' => 'nullable|integer|min:0',
            ]);
        }

        return $baseRules;
    }

    /**
     * Filter request data based on coupon usage
     */
    public function validationData()
    {
        $fields = ['name', 'started_at', 'expired_at'];

        if (!$this->hasUsedCoupons()) {
            $fields = array_merge($fields, [
                'value', 'usage_limit', 'trial_days', 'discount_month'
            ]);
        }

        return $this->only($fields);
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
        $response = new JsonResponse([
            'message' => 'Validation failed',
            'errors' => $errorDetails,
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
