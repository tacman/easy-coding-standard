<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210605\Symfony\Component\DependencyInjection\Compiler;

use ECSPrefix20210605\Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use ECSPrefix20210605\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210605\Symfony\Component\DependencyInjection\Definition;
use ECSPrefix20210605\Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
/**
 * Reads #[Autoconfigure] attributes on definitions that are autoconfigured
 * and don't have the "container.ignore_attributes" tag.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class RegisterAutoconfigureAttributesPass implements \ECSPrefix20210605\Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface
{
    private static $registerForAutoconfiguration;
    /**
     * {@inheritdoc}
     */
    public function process(\ECSPrefix20210605\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        if (80000 > \PHP_VERSION_ID) {
            return;
        }
        foreach ($container->getDefinitions() as $id => $definition) {
            if ($this->accept($definition) && null !== ($class = $container->getReflectionClass($definition->getClass()))) {
                $this->processClass($container, $class);
            }
        }
    }
    public function accept(\ECSPrefix20210605\Symfony\Component\DependencyInjection\Definition $definition) : bool
    {
        return 80000 <= \PHP_VERSION_ID && $definition->isAutoconfigured() && !$definition->hasTag('container.ignore_attributes');
    }
    public function processClass(\ECSPrefix20210605\Symfony\Component\DependencyInjection\ContainerBuilder $container, \ReflectionClass $class)
    {
        foreach ($class->getAttributes(\ECSPrefix20210605\Symfony\Component\DependencyInjection\Attribute\Autoconfigure::class, \ReflectionAttribute::IS_INSTANCEOF) as $attribute) {
            self::registerForAutoconfiguration($container, $class, $attribute);
        }
    }
    private static function registerForAutoconfiguration(\ECSPrefix20210605\Symfony\Component\DependencyInjection\ContainerBuilder $container, \ReflectionClass $class, \ReflectionAttribute $attribute)
    {
        if (self::$registerForAutoconfiguration) {
            return (self::$registerForAutoconfiguration)($container, $class, $attribute);
        }
        $parseDefinitions = new \ReflectionMethod(\ECSPrefix20210605\Symfony\Component\DependencyInjection\Loader\YamlFileLoader::class, 'parseDefinitions');
        $parseDefinitions->setAccessible(\true);
        $yamlLoader = $parseDefinitions->getDeclaringClass()->newInstanceWithoutConstructor();
        self::$registerForAutoconfiguration = static function (\ECSPrefix20210605\Symfony\Component\DependencyInjection\ContainerBuilder $container, \ReflectionClass $class, \ReflectionAttribute $attribute) use($parseDefinitions, $yamlLoader) {
            $attribute = (array) $attribute->newInstance();
            foreach ($attribute['tags'] ?? [] as $i => $tag) {
                if (\is_array($tag) && [0] === \array_keys($tag)) {
                    $attribute['tags'][$i] = [$class->name => $tag[0]];
                }
            }
            $parseDefinitions->invoke($yamlLoader, ['services' => ['_instanceof' => [$class->name => [$container->registerForAutoconfiguration($class->name)] + $attribute]]], $class->getFileName());
        };
        return (self::$registerForAutoconfiguration)($container, $class, $attribute);
    }
}