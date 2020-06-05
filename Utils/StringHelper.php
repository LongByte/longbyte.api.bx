<?php

namespace Api\Core\Utils;

/**
 * Class \Api\Core\Utils\StringHelper
 */
class StringHelper {

    public static function convertCodeToUpperCamelCase($strCode) {

        $strResult = '';
        $arParts = explode("_", $strCode);
        foreach ($arParts as $strPart) {
            $strResult .= ucfirst(strtolower($strPart));
        }
        return $strResult;
    }

}
