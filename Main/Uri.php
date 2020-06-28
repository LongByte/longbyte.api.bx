<?php

namespace Api\Core\Main;

/**
 * Class \Api\Core\Main\Uri
 *
 */
class Uri extends \Bitrix\Main\Web\Uri {

    private $params;

    /**
     * 
     * @global \CMain $APPLICATION
     * @param string $url
     */
    public function __construct($url = '') {
        if (strlen($url) == 0) {
            global $APPLICATION;
            $url = $APPLICATION->GetCurUri();
        }
        parent::__construct($url);
    }

    /**
     * 
     * @return array
     */
    public function getParams() {
        if (is_null($this->params)) {
            $arParams = array();
            parse_str($this->query, $arParams);
            $this->params = $arParams;
        }
        return $this->params;
    }

    /**
     * @param array $arParams
     * @return \Bitrix\Main\Web\Uri
     */
    public function deleteParams($arParams) {
        $this->params = null;
        return parent::deleteParams($arParams);
    }

    /**
     * 
     * @param string $strKey
     * @param string $strValue
     * @return $this
     */
    public function addParam($strKey, $strValue) {
        $this->getParams();
        $this->params[$strKey] = $strValue;
        $this->query = http_build_query($this->params, '', '&');
        return $this;
    }

    /**
     * @return string
     */
    public function getQueryString() {
        $strQuery = $this->getQuery();
        if (strlen($strQuery) > 0) {
            return '?' . $strQuery;
        }

        return '';
    }

    /**
     *
     * @param int $port
     * @return $this
     */
    public function setPort($port) {
        $this->port = $port;
        return $this;
    }

    /**
     *
     * @param string $scheme
     * @return $this
     */
    public function setScheme($scheme) {
        if ($scheme == 'https') {
            $this->setPort(443);
        } else {
            $this->setPort(80);
        }
        $this->scheme = $scheme;
        return $this;
    }

}
