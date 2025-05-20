<?php

namespace App\Http\Request;

use App\Exceptions\AuthException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCouponRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {

        return [
            'code' => 'required|string|max:255',
            'discount_id' => "required|integer|min:1|exists:.discounts,id",
            'shop' => $this->shopRules(),
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

    public function validationData(): array
    {
        return [
            'code' => $this->input('code'),
            'discount_id' => $this->input('discount_id'),
            'shop' => $this->input('shop'),
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
        throw AuthException::validateLogin($errorDetails);
    }
}
