<?php

namespace Api\Core\Main;

/**
 * Class \Api\Core\Main\HttpClient
 */
class HttpClient extends \Bitrix\Main\Web\HttpClient {

    /**
     * 
     * @param type $url
     * @param type $postData
     * @param type $multipart
     * @return boolean
     */
    public function put($url, $postData = null, $multipart = false) {
        if ($multipart) {
            $postData = $this->prepareMultipart($postData);
        }

        if ($this->query(self::HTTP_PUT, $url, $postData)) {
            return $this->getResult();
        }
        return false;
    }

    /**
     * 
     * @param type $url
     * @param type $postData
     * @param type $multipart
     * @return boolean
     */
    public function delete($url, $postData = null, $multipart = false) {
        if ($multipart) {
            $postData = $this->prepareMultipart($postData);
        }

        if ($this->query(self::HTTP_DELETE, $url, $postData)) {
            return $this->getResult();
        }
        return false;
    }

}
