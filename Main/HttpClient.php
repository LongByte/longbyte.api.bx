<?php

namespace Api\Core\Main;

/**
 * Class \Api\Core\Main\HttpClient
 */
class HttpClient extends \Bitrix\Main\Web\HttpClient
{
    public function put(string $url, mixed $postData = null, bool $multipart = false)
    {
        if ($multipart) {
            $postData = $this->prepareMultipart($postData);
        }

        if ($this->query(self::HTTP_PUT, $url, $postData)) {
            return $this->getResult();
        }
        return false;
    }

    public function delete(string $url, mixed $postData = null, bool $multipart = false)
    {
        if ($multipart) {
            $postData = $this->prepareMultipart($postData);
        }

        if ($this->query(self::HTTP_DELETE, $url, $postData)) {
            return $this->getResult();
        }
        return false;
    }

}
