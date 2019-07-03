<?php

namespace unit\Mocks;

class RedisCacheMock extends \Redis
{
    protected $cache;
    
    public function get($key)
    {
        if (isset($this->cache[$key])) {
            if (empty($cache[$key]['expires']) || $cache[$key]['expires'] > time())
            {
                return $this->cache[$key]['value'];
            } else {
                return null;
            }
        }

        return null;
    }
    
    public function set($key, $value, $ttl = null)
    {
        $expires = null;
        if (!$ttl) {
            $expires = time() + $ttl;
        }
        $this->cache[$key] = [
            'value' => $value,
            'ttl' => $expires
        ];
    }
}
