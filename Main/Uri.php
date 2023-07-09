<?php

namespace Api\Core\Main;

/**
 * Class \Api\Core\Main\Uri
 */
class Uri extends \Bitrix\Main\Web\Uri
{

    private $params;

    public function __construct(string $url = '')
    {
        if (strlen($url) == 0) {
            global $APPLICATION;
            $url = $APPLICATION->GetCurUri();
        }
        parent::__construct($url);
    }

    public function getParams(): array
    {
        if (is_null($this->params)) {
            $arParams = array();
            parse_str($this->query, $arParams);
            $this->params = $arParams;
        }
        return $this->params;
    }

    public function deleteParams(array $arParams, $preserveDots = false): self
    {
        $this->params = null;
        return parent::deleteParams($arParams, $preserveDots);
    }

    public function addParam(string $strKey, string $strValue): self
    {
        $this->getParams();
        $this->params[$strKey] = $strValue;
        $this->query = http_build_query($this->params, '', '&');
        return $this;
    }

    public function getQueryString(): string
    {
        $strQuery = $this->getQuery();
        if (strlen($strQuery) > 0) {
            return '?' . $strQuery;
        }

        return '';
    }

    public function setPort(int $port): self
    {
        $this->port = $port;
        return $this;
    }

    public function setScheme(string $scheme): self
    {
        if ($scheme == 'https') {
            $this->setPort(443);
        } else {
            $this->setPort(80);
        }
        $this->scheme = $scheme;
        return $this;
    }

    public function setHttps(bool $isHttps = true): self
    {
        if ($isHttps) {
            $this->setScheme('https');
        } else {
            $this->setScheme('http');
        }
        return $this;
    }

}
