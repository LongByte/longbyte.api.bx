<?php

namespace Api\Core\Utils;

/**
 * Class \Api\Core\Utils\Agent
 */
class Agent {

    /**
     * 
     * @param string $strModule
     * @param string $strController
     * @param string $strMethod
     * @return string
     */
    public static function executeController(string $strModule, string $strController = 'Index', string $strMethod = 'get') {

        $strControllerClass = "\\Api\\Controller\\{$strModule}\\{$strController}";
        if (class_exists($strControllerClass)) {
            $obController = new $strControllerClass();
            $obController->$strMethod();
        }

        $arParams = array();
        foreach (func_get_args() as $strParamValue) {
            $arParams[] = "'$strParamValue'";
        }

        return __METHOD__ . '(' . implode(', ', $arParams) . ');';
    }

}
