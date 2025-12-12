<?php

if (! function_exists('detectLang')) {
    function detectLang(?string $text): string
    {
        if (!$text) {
            return 'ar';
        }

        return preg_match('/\p{Arabic}/u', $text) ? 'ar' : 'en';
    }
}
