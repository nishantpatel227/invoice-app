<?php

namespace App\Helpers;

class CountryHelper
{
    public static function countryToCurrency($country)
    {
        $map = [
            'Canada' => 'CAD',
            'United States' => 'USD',
            'India' => 'INR',
            'United Kingdom' => 'GBP',
            'Germany' => 'EUR',
            'Australia' => 'AUD',
            // Add more as needed
        ];

        return $map[$country] ?? 'USD'; // default
    }

    public static function postalCodeRegex($country)
    {
        $patterns = [
            'Canada' => '/^[A-Za-z]\d[A-Za-z][ -]?\d[A-Za-z]\d$/',
            'United States' => '/^\d{5}(-\d{4})?$/',
            'India' => '/^\d{6}$/',
            'United Kingdom' => '/^([A-Z]{1,2}\d[A-Z\d]? ?\d[ABD-HJLNP-UW-Z]{2})$/i',
            'Germany' => '/^\d{5}$/',
            'Australia' => '/^\d{4}$/',
        ];

        return $patterns[$country] ?? null;
    }
}
