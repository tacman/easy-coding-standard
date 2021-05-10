<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210510\Symfony\Component\DependencyInjection\Loader\Configurator;

use ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
class ParametersConfigurator extends \ECSPrefix20210510\Symfony\Component\DependencyInjection\Loader\Configurator\AbstractConfigurator
{
    const FACTORY = 'parameters';
    private $container;
    public function __construct(\ECSPrefix20210510\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $this->container = $container;
    }
    /**
     * Creates a parameter.
     *
     * @return $this
     * @param string $name
     */
    public final function set($name, $value)
    {
        $name = (string) $name;
        $this->container->setParameter($name, static::processValue($value, \true));
        return $this;
    }
    /**
     * Creates a parameter.
     *
     * @return $this
     * @param string $name
     */
    public final function __invoke($name, $value)
    {
        $name = (string) $name;
        return $this->set($name, $value);
    }
}
