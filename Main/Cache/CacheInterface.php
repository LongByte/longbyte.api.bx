<?php

namespace Api\Core\Main\Cache;

interface CacheInterface
{

    public function get($id, $path);

    public function set($id, $path, $value, int $duration = null);

    public function exists($id, $path);

    public function delete($id, $path);

    public function clean();
}
