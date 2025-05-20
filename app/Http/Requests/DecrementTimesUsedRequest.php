<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class DecrementTimesUsedRequest extends FormRequest
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
            'numDecrement' => ['required', 'integer', 'min:0'],
        ];
    }

    public function validationData(): array
    {
        return [
            'numDecrement' => $this->input('numDecrement'),
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
        $response = new JsonResponse([
            'message' => 'Validation failed',
            'errors' => $errorDetails,
        ], 422);

        throw new ValidationException($validator, $response);
    }
}
