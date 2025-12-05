<?php

namespace App\Support;

class ArabicNameNormalizer
{
    public static function normalize(string $name): string
    {
        $name = trim($name);
        $name = mb_strtolower($name, 'UTF-8');

        $replacements = [
            'أ' => 'ا',
            'إ' => 'ا',
            'آ' => 'ا',
            'ٱ' => 'ا',
            'ة' => 'ه',  
            'ى' => 'ي',  
        ];

        $name = strtr($name, $replacements);

        $tashkeelPattern = '/[\x{0610}-\x{061A}\x{064B}-\x{065F}\x{0670}\x{06D6}-\x{06DC}\x{06DF}-\x{06E8}\x{06EA}-\x{06ED}]/u';
        $name = preg_replace($tashkeelPattern, '', $name);

        $name = preg_replace('/\bعبد\s+/u', 'عبد', $name);

        $name = preg_replace('/\s+/u', '', $name);

        return $name;
    }

 
    public static function firstArabicName(?string $fullName): ?string
    {
        if (!$fullName) {
            return null;
        }

        $fullName = trim($fullName);
        $parts = preg_split('/\s+/u', $fullName);

        if (count($parts) === 0) {
            return null;
        }

        if ($parts[0] === 'عبد' && isset($parts[1])) {
            return $parts[0] . ' ' . $parts[1];
        }

        return $parts[0];
    }
}
