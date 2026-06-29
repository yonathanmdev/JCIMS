<?php
namespace App\Helpers;
class AmharicNormalizer {
    
    private static $map = [
        // h-series
        'ሃ'=>'ሀ','ሐ'=>'ሀ','ሓ'=>'ሀ','ኀ'=>'ሀ','ኃ'=>'ሀ',
        'ሑ'=>'ሁ','ኁ'=>'ሁ',
        'ሒ'=>'ሂ','ኂ'=>'ሂ',
        'ሔ'=>'ሄ','ኄ'=>'ሄ',
        'ሕ'=>'ህ','ኅ'=>'ህ',
        'ሖ'=>'ሆ','ኆ'=>'ሆ',
        // s-series
        'ሠ'=>'ሰ', 'ሡ'=>'ሱ', 'ሢ'=>'ሲ', 'ሣ'=>'ሳ', 'ሤ'=>'ሴ', 'ሥ'=>'ስ', 'ሦ'=>'ሶ',
        // ts-series
        'ጸ'=>'ፀ', 'ጹ'=>'ፁ', 'ጺ'=>'ፂ', 'ጻ'=>'ፃ', 'ጼ'=>'ፄ', 'ጽ'=>'ፅ', 'ጾ'=>'ፆ',
        // a-series
        'ኣ'=>'አ','ዐ'=>'አ','ዓ'=>'አ',
        'ዑ'=>'ኡ', 'ዒ'=>'ኢ', 'ዔ'=>'ኤ', 'ዕ'=>'እ', 'ዖ'=>'ኦ',
    ];

    /**
     * የአማርኛ ፊደላትን አንድ አይነት ያደርጋል
     */
    public static function normalize($text) {
        if (empty($text)) return '';

        // ፊደላቱን መቀየር
        $text = str_replace(array_keys(self::$map), array_values(self::$map), $text);

        // ትርፍ ክፍተቶችን ማስወገድ
        $text = preg_replace('/\s+/', '', trim($text));

        return $text;
    }
  public static function normalizeOnly($text) {
        if (empty($text)) return '';

        // ፊደላቱን መቀየር
        $text = str_replace(array_keys(self::$map), array_values(self::$map), $text);

        return $text;
    }
}