<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20220606\Symfony\Component\Config\Loader;

use ECSPrefix20220606\Symfony\Component\Config\Exception\LoaderLoadException;
/**
 * DelegatingLoader delegates loading to other loaders using a loader resolver.
 *
 * This loader acts as an array of LoaderInterface objects - each having
 * a chance to load a given resource (handled by the resolver)
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class DelegatingLoader extends \ECSPrefix20220606\Symfony\Component\Config\Loader\Loader
{
    public function __construct(\ECSPrefix20220606\Symfony\Component\Config\Loader\LoaderResolverInterface $resolver)
    {
        $this->resolver = $resolver;
    }
    /**
     * {@inheritdoc}
     * @param mixed $resource
     * @return mixed
     */
    public function load($resource, string $type = null)
    {
        if (\false === ($loader = $this->resolver->resolve($resource, $type))) {
            throw new \ECSPrefix20220606\Symfony\Component\Config\Exception\LoaderLoadException($resource, null, 0, null, $type);
        }
        return $loader->load($resource, $type);
    }
    /**
     * {@inheritdoc}
     * @param mixed $resource
     */
    public function supports($resource, string $type = null) : bool
    {
        return \false !== $this->resolver->resolve($resource, $type);
    }
}
