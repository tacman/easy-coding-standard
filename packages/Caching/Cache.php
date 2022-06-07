<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\EasyCodingStandard\Caching;

use ECSPrefix20220607\Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage;
final class Cache
{
    /**
     * @var \Symplify\EasyCodingStandard\Caching\ValueObject\Storage\FileCacheStorage
     */
    private $fileCacheStorage;
    public function __construct(FileCacheStorage $fileCacheStorage)
    {
        $this->fileCacheStorage = $fileCacheStorage;
    }
    /**
     * @return mixed
     */
    public function load(string $key, string $variableKey)
    {
        return $this->fileCacheStorage->load($key, $variableKey);
    }
    /**
     * @param mixed $data
     */
    public function save(string $key, string $variableKey, $data) : void
    {
        $this->fileCacheStorage->save($key, $variableKey, $data);
    }
    public function clear() : void
    {
        $this->fileCacheStorage->clear();
    }
    public function clean(string $cacheKey) : void
    {
        $this->fileCacheStorage->clean($cacheKey);
    }
}
