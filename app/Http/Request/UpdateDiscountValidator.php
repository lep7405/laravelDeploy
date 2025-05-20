<?php

namespace App\Http\Request;

use App\Exceptions\DiscountException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UpdateDiscountValidator
{
    private const RESTRICTED_FIELDS = ['type', 'value', 'trial_days', 'discount_for_x_month', 'discount_month'];

    /**
     * Validate discount data for editing
     *
     * @param  array  $data  The data to validate
     * @param  bool  $discountStatus  Whether the discount has been used
     * @param  string  $databaseName  The database name
     * @return array Validated data
     *
     * @throws DiscountException
     * @throws ValidationException
     */
    public static function validateUpdate(array $data, bool $discountStatus, string $databaseName): array
    {
        $rules = self::getBaseRules();

        if ($discountStatus) {
            self::validateUsedDiscount($data);
        } else {
            $rules = array_merge($rules, self::getUnusedDiscountRules($data, $databaseName));
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            $errors = $validator->errors();
            $errorDetails = [];

            foreach ($errors->messages() as $field => $messages) {
                foreach ($messages as $message) {
                    $errorDetails[$field][] = $message;
                }
            }
            throw DiscountException::validateUpdate($errorDetails);
        }
        return $validator->validated();
    }

    /**
     * Get base validation rules that apply to all discounts
     */
    private static function getBaseRules(): array
    {
        return [
            'name' => 'required|max:255|string',
            'started_at' => 'nullable|date',
            'expired_at' => 'nullable|date|after_or_equal:started_at',
            'usage_limit' => 'nullable|integer|min:0',
        ];
    }

    /**
     * Validate that restricted fields aren't being updated for used discounts
     */
    private static function validateUsedDiscount(array $data): void
    {
        $invalidFields = array_keys(Arr::only($data, self::RESTRICTED_FIELDS));

        if (! empty($invalidFields)) {
            throw DiscountException::restrictUpdateFieldsForUsedDiscount();
        }
    }

    /**
     * Get additional rules for unused discounts
     */
    private static function getUnusedDiscountRules(array $data, string $databaseName): array
    {
        $rules = [
            'type' => 'required|in:percentage,amount',
            'trial_days' => 'nullable|integer|min:0',
        ];

        // Value rules based on type
        if (isset($data['type'])) {
            if ($data['type'] === 'percentage') {
                $rules['value'] = 'nullable|numeric|between:0,100';
            } elseif ($data['type'] === 'amount') {
                $rules['value'] = 'nullable|numeric|min:0';
            }
        }

        // Special database rules
        $rules['discount_month'] = 'required|integer|min:1';

        return $rules;
    }

    /**
     * Format and throw validation exception
     *
     * @throws DiscountException
     */
    private static function throwValidationException($validator): void
    {
        $errors = $validator->errors();
        $errorDetails = [];

        foreach ($errors->messages() as $field => $messages) {
            foreach ($messages as $message) {
                $errorDetails[$field][] = $message;
            }
        }
        throw DiscountException::validateUpdate($errorDetails);
    }
}
