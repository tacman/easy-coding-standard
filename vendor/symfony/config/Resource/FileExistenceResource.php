<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Config\Resource;

/**
 * FileExistenceResource represents a resource stored on the filesystem.
 * Freshness is only evaluated against resource creation or deletion.
 *
 * The resource can be a file or a directory.
 *
 * @author Charles-Henri Bruyand <charleshenri.bruyand@gmail.com>
 *
 * @final
 */
class FileExistenceResource implements \ECSPrefix20210508\Symfony\Component\Config\Resource\SelfCheckingResourceInterface
{
    private $resource;
    private $exists;
    /**
     * @param string $resource The file path to the resource
     */
    public function __construct($resource)
    {
        $resource = (string) $resource;
        $this->resource = $resource;
        $this->exists = \file_exists($resource);
    }
    /**
     * {@inheritdoc}
     * @return string
     */
    public function __toString()
    {
        return $this->resource;
    }
    /**
     * @return string The file path to the resource
     */
    public function getResource()
    {
        return $this->resource;
    }
    /**
     * {@inheritdoc}
     * @param int $timestamp
     * @return bool
     */
    public function isFresh($timestamp)
    {
        $timestamp = (int) $timestamp;
        return \file_exists($this->resource) === $this->exists;
    }
}