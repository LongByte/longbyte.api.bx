<?php

namespace Api\Core\Form\Validate\GoogleCaptcha;

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Web\HttpClient;

class Request {

    private $gRecaptchaResponse = '';
    private $secretkey = '';

    /**
     * 
     * @param type $gRecaptchaResponse
     */
    public function __construct($gRecaptchaResponse) {
        $this->gRecaptchaResponse = $gRecaptchaResponse;
        $this->secretkey = Option::get('realweb.api', 'recaptcha-secretkey');
    }

    /**
     * 
     * @return boolean
     */
    public function check() {

        if (strlen($this->gRecaptchaResponse) <= 0) {
            return false;
        }

        $obServer = Application::getInstance()->getContext()->getServer();
        $strUrl = 'https://www.google.com/recaptcha/api/siteverify?secret=' . $this->secretkey . '&response=' . $this->gRecaptchaResponse . '&remoteip=' . $obServer->get('REMOTE_ADDR');
        $obHttpClient = new HttpClient();
        $strResponce = $obHttpClient->get($strUrl);
        $arResponse = json_decode($strResponce, true);
        return $arResponse['success'];
    }

}
