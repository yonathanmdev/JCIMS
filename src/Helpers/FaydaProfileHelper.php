<?php
declare(strict_types=1);

namespace App\Helpers;

final class FaydaProfileHelper
{
    /**
     * Fayda/eSignet returns locale-tagged fields as suffixed keys,
     * e.g. "name#am", "name#en", "gender#am", "gender#en" —
     * rather than a single "name" key. This pulls the best available
     * value: preferred locale, then English, then a bare key if one
     * ever exists (some fields like birthdate/phone_number have no
     * locale suffix at all).
     */
    public static function field(array $profile, string $key, string $preferredLang = 'am'): string
    {
        return $profile["{$key}#{$preferredLang}"]
            ?? $profile["{$key}#en"]
            ?? $profile[$key]
            ?? '';
    }
}