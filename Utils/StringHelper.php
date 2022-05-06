<?php

namespace Api\Core\Utils;

/**
 * Class \Api\Core\Utils\StringHelper
 */
class StringHelper
{

    public static function convertCodeToUpperCamelCase(string $strCode): string
    {
        $strResult = '';
        $arParts = explode("_", $strCode);
        foreach ($arParts as $strPart) {
            $strResult .= ucfirst(strtolower($strPart));
        }
        return $strResult;
    }

    public static function getTranslit(string $string): string
    {
        $params = array('replace_space' => '-', 'replace_other' => '-');
        return \CUtil::translit($string, 'ru', $params);
    }

}
