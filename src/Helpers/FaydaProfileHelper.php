<?php
declare(strict_types=1);

/**
 * Fayda/eSignet returns locale-tagged fields as suffixed keys,
 * e.g. "name#am", "name#en", "gender#am", "gender#en" —
 * rather than a single "name" key. This pulls the best available
 * value: preferred locale, then English, then a bare key (some
 * fields like birthdate/phone_number have no locale suffix at all).
 */
function fayda_field(array $profile, string $key, string $lang = 'am'): string {
    return $profile["{$key}#{$lang}"]
        ?? $profile["{$key}#en"]
        ?? $profile[$key]
        ?? '';
}

/**
 * Address is nested one level deeper — each sub-field is also
 * locale-suffixed inside the locale-suffixed address array.
 * Not used on the confirm page currently; kept here for when
 * address display/storage is needed later.
 */
function fayda_address_field(array $profile, string $subfield, string $lang = 'am'): string {
    $address = $profile["address#{$lang}"] ?? $profile['address#en'] ?? [];
    return $address["{$subfield}#{$lang}"] ?? $address["{$subfield}#en"] ?? '';
}