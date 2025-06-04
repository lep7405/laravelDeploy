<?php
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

if (!function_exists('handleFormRequestValidationFailure')) {
    /**
     * Handle FormRequest validation failures consistently
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @return void
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    function handleFormRequestValidationFailure(Validator $validator)
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

        throw new HttpResponseException($response);
    }
}
