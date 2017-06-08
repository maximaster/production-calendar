<?php

namespace Maximaster\ProductionCalendar\RulesProvider;

use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\Cache;
use Exception;

class CacheProvider implements ProviderInterface
{
    /**
     * @var ProviderInterface
     */
    protected $parent;

    /**
     * @var string
     */
    protected $cacheDir;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * ProviderInterface constructor.
     * @param ProviderInterface|null $parentProvider
     * @throws Exception
     */
    public function __construct(ProviderInterface $parentProvider)
    {
        $this->parent = $parentProvider;
    }

    public function getInner()
    {
        if ($this->cache) {
            return $this->cache;
        }

        $file = new File;
        $file->setOption('ttl', 3600);
        return $this->cache = new Cache($file);
    }

    public function clear()
    {
        $cacheKey = $this->getKey();
        $this->getInner()->delete($cacheKey);
        return $this;
    }

    protected function getKey()
    {
        return md5(__METHOD__.get_class($this->parent));
    }

    /**
     * {@inheritdoc}
     */
    public function get()
    {
        $cache = $this->getInner();
        $cacheKey = $this->getKey();

        if ($rules = $cache->get($cacheKey)) {
            return $rules;
        }

        $result = $this->parent->get();
        $cache->set($cacheKey, $result);

        return $result;
    }
}