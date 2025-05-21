<?php

namespace App\Http\Requests;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CreateCouponRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        Log::debug('Validation data:', [
            'code' => $this->input('code'),
            'discountId' => $this->input('discountId'),
            'shop' => $this->input('shop'),
        ]);
        return [
            'code' => "required|string|max:128|unique:coupons,code",
            'discountId' => "required|integer|min:1|exists:discounts,id",
            'shop' => $this->shopRules(),
        ];
    }

    public function validationData(): array
    {
        return [
            'code' => $this->input('code'),
            'discountId' => $this->input('discountId'),
            'shop' => $this->input('shop'),
        ];
    }
    protected function shopRules()
    {
        return function ($attribute, $value, $fail) {

            if (is_null($value)) {
                return;
            }

            if (!is_string($value)) {
                $fail("The $attribute must be a string.");
                return;
            }

            if (strlen($value) > 255) {
                $fail("The $attribute may not be greater than 255 characters.");
                return;
            }
            $shopDomain = preg_replace('#^https?://#', '', $value);

            if (!preg_match('/^[a-zA-Z0-9-.]+$/', $shopDomain)) {
                $fail("The $attribute contains invalid characters. Only letters, numbers, hyphens, and dots are allowed.");
                return;
            }
            if (!preg_match('/\.myshopify\.com$/i', $shopDomain)) {
                $shopDomain .= '.myshopify.com';
            }
            $url = "https://{$shopDomain}";

            if (filter_var($url, FILTER_VALIDATE_URL) === false) {
                $fail("The $attribute is not a valid URL.");
                return;
            }

            if (!preg_match('/^https:\/\/[a-zA-Z0-9-]+\.myshopify\.com$/i', $url)) {
                $fail("The $attribute must be a valid Shopify URL (e.g., 'https://shop-name.myshopify.com').");
                return;
            }
            $client = new Client();
            try {
                $response = $client->get($url, [
                    'http_errors' => false,
                    'timeout' => 5,
                ]);

                $statusCode = $response->getStatusCode();

                if ($statusCode === 200) {
                    return;
                } elseif ($statusCode === 404) {
                    $fail("The shop '$shopDomain' not found");
                } else {

                    $fail("Unable to verify the shop '$shopDomain'");
                }
            } catch (RequestException $e) {
                $fail("Could not verify the shop '$shopDomain'");
            }
        };
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
