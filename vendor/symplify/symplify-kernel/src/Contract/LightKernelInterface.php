<?php

declare (strict_types=1);
namespace ECSPrefix20220307\Symplify\SymplifyKernel\Contract;

use ECSPrefix20220307\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20220307\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20220307\Psr\Container\ContainerInterface;
}
