<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210507\Symfony\Component\Cache\DependencyInjection;

use ECSPrefix20210507\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210507\Symfony\Component\DependencyInjection\Reference;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class CachePoolClearerPass implements \ECSPrefix20210507\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private $cachePoolClearerTag;
    /**
     * @param string $cachePoolClearerTag
     */
    public function __construct($cachePoolClearerTag = 'cache.pool.clearer')
    {
        $this->cachePoolClearerTag = $cachePoolClearerTag;
    }
    /**
     * {@inheritdoc}
     * @param \ECSPrefix20210507\Symfony\Component\DependencyInjection\ContainerBuilder $container
     */
    public function process($container)
    {
        $container->getParameterBag()->remove('cache.prefix.seed');
        foreach ($container->findTaggedServiceIds($this->cachePoolClearerTag) as $id => $attr) {
            $clearer = $container->getDefinition($id);
            $pools = [];
            foreach ($clearer->getArgument(0) as $name => $ref) {
                if ($container->hasDefinition($ref)) {
                    $pools[$name] = new \ECSPrefix20210507\Symfony\Component\DependencyInjection\Reference($ref);
                }
            }
            $clearer->replaceArgument(0, $pools);
        }
    }
}
