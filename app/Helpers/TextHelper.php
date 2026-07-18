<?php
// في App\Helpers\TextHelper.php
namespace App\Helpers;

class TextHelper {
    public static function getFirstChar(string $name): string {
        if (empty($name)) return '';
        
        $firstChar = mb_substr($name, 0, 1, 'UTF-8');
        
        // إذا كان حرف إنجليزي
        if (preg_match('/^[a-zA-Z]/', $firstChar)) {
            return strtoupper($firstChar);
        }
        
        return $firstChar;
    }
}