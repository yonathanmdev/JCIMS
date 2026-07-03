<?php
namespace App\Helpers;

class BranchNameHelper
{
    /**
     * @param array $levelNames  [1 => level1 name, 2 => ..., 3 => ..., 4 => ...]
     * @param bool  $isKetemaAstedader  true if branch is under city administration (ketema_astedader = 'on')
     */
    public static function getFullBranchName(array $levelNames, bool $isKetemaAstedader): string
    {
        $level1 = $levelNames[1] ?? '';
        $level2 = $levelNames[2] ?? '';
        $level3 = $levelNames[3] ?? '';
        $level4 = $levelNames[4] ?? '';

        // No hierarchy resolved at all → treat as headquarters/system-wide user
        if ($level1 === '') {
            return 'ዋናው መስሪያ ቤት (Headquarters)';
        }

        if ($isKetemaAstedader) {
            return "በአብክመ {$level1} {$level2} ከተማ አስተዳደር የ{$level3} ክፍለ ከተማ የ{$level4} አንድ ማዕከል መስጫ ጣቢያ";
        }

        return "በአብክመ {$level1} {$level2} መምሪያ የ{$level3} ወረዳ የ{$level4} አንድ ማዕከል መስጫ ጣቢያ";
    }
}