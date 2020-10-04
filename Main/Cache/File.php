<?php

namespace Api\Core\Main\Cache;

class File implements CacheInterface {

    const DURATION = 86400;

    protected $_cache_dir = 'cache';
    protected $_cache_file_suffix = '.bin';
    protected $_dir_mode = 0755;
    protected $_file_mode = 0644;
    protected $_igbinary = false;

    function __construct() {
        $this->setIgbinary(\extension_loaded('igbinary'));
    }

    public function buildCacheId($strId) {
        if (is_string($strId)) {
            return $strId;
        } else {
            return md5($this->serialize($strId));
        }
    }

    public function serialize($value) {
        if ($this->isIgbinary()) {
            return igbinary_serialize($value);
        } else {
            return serialize($value);
        }
    }

    public function unserialize($value) {
        if ($this->isIgbinary()) {
            return igbinary_unserialize($value);
        } else {
            return unserialize($value);
        }
    }

    /**
     * @return string
     */
    public function getCacheFileSuffix(): string {
        return $this->_cache_file_suffix;
    }

    /**
     * @param string $strCacheFileSuffix
     * @return $this
     */
    public function setCacheFileSuffix(string $strCacheFileSuffix) {
        $this->_cache_file_suffix = $strCacheFileSuffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getCacheDir(): string {
        return $this->_cache_dir;
    }

    /**
     * @param string $strCacheDir
     * @return $this
     */
    public function setCacheDir(string $strCacheDir) {
        $this->_cache_dir = $strCacheDir;

        return $this;
    }

    /**
     * @return int
     */
    public function getDirMode(): int {
        return $this->_dir_mode;
    }

    /**
     * @param int $iDirMode
     * @return $this
     */
    public function setDirMode(int $iDirMode) {
        $this->_dir_mode = $iDirMode;

        return $this;
    }

    /**
     * @return int
     */
    public function getFileMode(): int {
        return $this->_file_mode;
    }

    /**
     * @param int $iFileMode
     * @return $this
     */
    public function setFileMode(int $iFileMode) {
        $this->_file_mode = $iFileMode;

        return $this;
    }

    /**
     * @return bool
     */
    public function isIgbinary(): bool {
        return $this->_igbinary;
    }

    /**
     * @param bool $bIgbinary
     * @return $this
     */
    public function setIgbinary(bool $bIgbinary = false) {
        $this->_igbinary = $bIgbinary;

        return $this;
    }

    public function getDocumentRoot() {
        return $_SERVER['DOCUMENT_ROOT'];
    }

    protected function getCacheFile(string $strId, string $strPath) {
        $arPath = array(
            $this->getDocumentRoot(),
            trim($this->getCacheDir(), DIRECTORY_SEPARATOR),
            trim($strPath, DIRECTORY_SEPARATOR),
            substr($strId, 0, 2),
            $strId . $this->getCacheFileSuffix()
        );

        return implode(DIRECTORY_SEPARATOR, $arPath);
    }

    public function exists($id, $path) {
        $cacheFile = $this->getCacheFile($this->buildCacheId($id), $path);

        return @filemtime($cacheFile) > time();
    }

    public function createDirectory($path) {
        $strDir = dirname($path);
        if (is_dir($strDir)) {
            return true;
        }
        if (!mkdir($strDir, $this->getDirMode(), true)) {
            return false;
        }

        return true;
    }

    public function set($id, $path, $value, int $duration = null) {
        $strId = $this->buildCacheId($id);
        $strCacheFile = $this->getCacheFile($strId, $path);
        if ($this->createDirectory($strCacheFile)) {
            $value = $this->serialize($value);
            if (@file_put_contents($strCacheFile, $value, LOCK_EX) !== false) {
                if ($this->getFileMode() !== null) {
                    @chmod($strCacheFile, $this->getFileMode());
                }
                if ($duration <= 0) {
                    $duration = self::DURATION;
                }

                return @touch($strCacheFile, $duration + time());
            }
        }

        return false;
    }

    public function get($id, $path) {
        $strId = $this->buildCacheId($id);
        $strCacheFile = $this->getCacheFile($strId, $path);
        if (@filemtime($strCacheFile) > time()) {
            $fp = @fopen($strCacheFile, 'r');
            if ($fp !== false) {
                @flock($fp, LOCK_SH);
                $cacheValue = @stream_get_contents($fp);
                @flock($fp, LOCK_UN);
                @fclose($fp);
                if ($cacheValue != false) {
                    $cacheValue = $this->unserialize($cacheValue);
                    if ($cacheValue !== false) {
                        return $cacheValue;
                    }
                }
            }
        }

        return false;
    }

    public function delete($id, $path) {
        $strId = $this->buildCacheId($id);
        $strCacheFile = $this->getCacheFile($strId, $path);

        return @unlink($strCacheFile);
    }

    public function clean() {
        $strPath = $this->getDocumentRoot() . DIRECTORY_SEPARATOR . trim($this->getCacheDir(), DIRECTORY_SEPARATOR);
        $this->_cleanPath($strPath, false);
    }

    public function cleanExpired() {
        $strPath = $this->getDocumentRoot() . DIRECTORY_SEPARATOR . trim($this->getCacheDir(), DIRECTORY_SEPARATOR);
        $this->_cleanPath($strPath, true);
    }

    protected function _cleanPath($path, $expiredOnly) {
        if (($handle = opendir($path)) !== false) {
            while (($file = readdir($handle)) !== false) {
                if ($file[0] === '.') {
                    continue;
                }
                $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                if (is_dir($fullPath)) {
                    $this->_cleanPath($fullPath, $expiredOnly);
                    if ($this->_isEmptyDir($fullPath)) {
                        @rmdir($fullPath);
                    }
                    if (!$expiredOnly) {
                        @rmdir($fullPath);
                    }
                } elseif (!$expiredOnly || $expiredOnly && @filemtime($fullPath) < time()) {
                    @unlink($fullPath);
                }
            }
            closedir($handle);
        }
    }

    private function _isEmptyDir($dir) {
        if (is_readable($dir) && (count(scandir($dir)) == 2)) {
            return true;
        } else {
            return false;
        }
    }

}
