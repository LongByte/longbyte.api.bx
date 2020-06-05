<?php

namespace Api\Core\Main;

use Bitrix\Main\Context;
use Bitrix\Main\Web\Uri;

/**
 * Class \Api\Core\Main\Cache
 */
class Cache {

    const TIME = 86400;

    /**
     * @var string
     */
    private $_id;

    /**
     * @var string
     */
    private $_dir;

    /**
     *
     * @var integer
     */
    private $_time;

    /**
     * @var array
     */
    private $_tag = array();

    /**
     * @var array
     */
    private $_global_tag = array();

    /**
     *
     * @var boolean
     */
    private $_has_dead_cache = false;

    /**
     *
     * @var boolean
     */
    private $_global = false;

    /**
     *
     * @var array
     */
    private $_params = array();

    /**
     *
     * @var boolean
     */
    private $_cache = true;
    private $_start_caching = false;
    private $_change_params = true;

    /**
     *
     * @var \Api\Core\Main\Cache
     */
    private static $instance;

    protected function __construct() {
        
    }

    /**
     *
     * @return \Api\Core\Main\Cache
     */
    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new Self();
        }
        /**
         * Очистим $_id,$_dir,$_tag
         */
        self::$instance->clearParams();
        return self::$instance;
    }

    function getParams() {
        usort($this->_params, function ($a, $b) {
            return strcmp($a["id"], $b["id"]);
        });

        return $this->_params;
    }

    /**
     *
     * @param array $params
     * @return $this
     */
    function setParams($params) {
        $this->_params[] = $params;
        return $this;
    }

    /**
     *
     * @return boolean
     */
    function getGlobal() {
        return $this->_global;
    }

    /**
     *
     * @return boolean
     */
    function isGlobal() {
        return $this->_global;
    }

    /**
     *
     * @param boolean $global
     * @return $this
     */
    function setGlobal($global = true) {
        $this->_global = $global;
        return $this;
    }

    private function clearParams() {

        if ($this->_start_caching === true) {
            $this->_change_params = false;
            return;
        }

        $this->_id = null;
        $this->_dir = null;
        $this->_time = null;
        $this->_tag = array();
        $this->_cache = true;
        $this->_change_params = true;

        $this->_start_caching = true;
    }

    /**
     * @param $id
     * @return $this
     */

    /**
     * @param $id
     * @return $this
     */
    public function setId($id) {
        if ($this->_change_params) {
            $this->_id = md5(serialize($id));
        }
        return $this;
    }

    /**
     * @param $dir
     * @return $this
     */
    public function setDir($dir) {
        if ($this->_change_params) {
            $this->_dir = SITE_ID . '/' . str_replace("\\", "/", $dir);
        }
        return $this;
    }

    /**
     * @param $tag
     * @return $this
     */
    public function setTag($tag) {
        $this->_tag[] = $tag;
        if (!in_array($tag, $this->_global_tag)) {
            $this->_global_tag[] = $tag;
        }
        return $this;
    }

    /**
     * 
     * @param int $iIblockId
     * @return $this
     */
    public function setIblockTag($iIblockId) {
        $this->setTag('iblock_id_' . $iIblockId);
        $this->setTag('iblock_id_meta_' . $iIblockId);
        return $this;
    }

    /**
     * @return string
     */
    public function getDir() {
        return $this->_dir;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * @return array
     */
    public function getTag() {
        return $this->_tag;
    }

    /**
     * @return array
     */
    public function getGlobalTag() {
        return $this->_global_tag;
    }

    /**
     *
     * @param integer $iTime
     * @return $this
     */
    public function setTime($iTime) {
        if ($this->_change_params) {
            $this->_time = $iTime;
        }
        return $this;
    }

    /**
     *
     * @return int
     */
    function getTime() {
        if (is_null($this->_time)) {
            $this->_time = self::TIME;
        }
        return $this->_time;
    }

    private function checkParams() {
        $strId = $this->getId();
        $strDir = $this->getDir();
        if (strlen($strId) == 0) {
            throw new \Exception("method setId is required");
        }

        if (strlen($strDir) == 0) {
            $obUri = new Uri(Context::getCurrent()->getRequest()->getRequestUri());
            $this->setDir($obUri->getPath());
        }
    }

    /**
     * @param $callable
     * @return array|mixed
     * @throws \Exception
     */
    public function get($callable) {
        $this->checkParams();

        global $CACHE_MANAGER;
        $arResult = [];
        $cache = \Bitrix\Main\Data\Cache::createInstance();

        if ($cache->initCache($this->getTime(), $this->getId(), $this->getDir())) {
            $arResult = $cache->getVars();
        } elseif ($cache->startDataCache()) {

            $this->_has_dead_cache = true;
            $arResult = call_user_func($callable);
            if (count($this->getTag()) > 0) {
                $CACHE_MANAGER->StartTagCache($this->getDir());
            }
            if (!empty($arResult) && $this->isCaching()) {
                if (count($this->getTag()) > 0) {
                    foreach ($this->getTag() as $strTag) {
                        $CACHE_MANAGER->RegisterTag($strTag);
                    }
                    $CACHE_MANAGER->endTagCache();
                }
                $cache->endDataCache($arResult);
            } else {
                $cache->abortDataCache();
            }
        }

        //ведь зависит еще и от контента
        $this->setParams(array(
            'id' => $this->getId(),
            'dir' => $this->getDir(),
            'content' => md5(serialize($arResult)),
        ));
        $this->_start_caching = false;

        return $arResult;
    }

    public function isCaching() {
        return $this->_cache;
    }

    public function abortCache() {
        $this->_cache = false;
        return $this;
    }

    public function hasDeadCache() {
        return $this->_has_dead_cache;
    }

}
