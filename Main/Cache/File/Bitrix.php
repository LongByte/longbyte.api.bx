<?php

namespace Api\Core\Main\Cache\File;

use Bitrix\Main\Data\CacheEngineFiles;
use Api\Core\Main\Cache;

require_once __DIR__ . '/../CacheInterface.php';
require_once __DIR__ . '/../File.php';

class Bitrix extends CacheEngineFiles {

    /**
     * @var Cache\File
     */
    private $_cache = null;

    public function __construct($options = []) {
        parent::__construct($options);
        $this->getCache()->setDirMode(BX_DIR_PERMISSIONS);
        $this->getCache()->setFileMode(BX_FILE_PERMISSIONS);
    }

    /**
     * @return Cache\File
     */
    public function getCache() {
        if ($this->_cache === null) {
            $this->_cache = new Cache\File();
        }

        return $this->_cache;
    }

    public function isCacheExpired($path) {
        if (!file_exists($path) || @filemtime($path) < time()) {
            return true;
        }
    }

    public function read(&$allVars, $baseDir, $initDir, $filename, $TTL) {
        $strId = pathinfo($filename, PATHINFO_FILENAME);
        $this->getCache()->setCacheDir($baseDir);
        $this->getCache()->setCacheFileSuffix('.php');
        $allVars = $this->getCache()->get($strId, $initDir);

        return $allVars !== false;
    }

    public function write($allVars, $baseDir, $initDir, $filename, $TTL) {
        $strId = pathinfo($filename, PATHINFO_FILENAME);
        $this->getCache()->setCacheFileSuffix('.php');
        $this->getCache()->setCacheDir($baseDir);

        return $this->getCache()->set($strId, $initDir, $allVars, $TTL);
    }

}
