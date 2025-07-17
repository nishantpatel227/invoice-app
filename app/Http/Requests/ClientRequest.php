<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Helpers\CountryHelper;

class ClientRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['nullable', 'email'],
            'billing_country' => ['nullable', 'string'],
            'billing_postal_code' => ['nullable', function ($attribute, $value, $fail) {
                $country = $this->input('billing_country');
                $pattern = CountryHelper::postalCodeRegex($country);

                if ($pattern && !preg_match($pattern, $value)) {
                    $fail("The $attribute is invalid for $country.");
                }
            }],
            // Add more validation as needed
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
