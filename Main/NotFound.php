<?php

namespace Api\Core\Main;

/*
 * Class \Api\Core\Main\NotFound
 */

class NotFound
{

    public static function setStatus404()
    {
        \Bitrix\Iblock\Component\Tools::process404('404 Not Found', true, true, true);
    }

}
