<?php

declare (strict_types=1);
namespace ECSPrefix20211214\Symplify\SymplifyKernel\Contract;

use ECSPrefix20211214\Psr\Container\ContainerInterface;
/**
 * @api
 */
interface LightKernelInterface
{
    /**
     * @param string[] $configFiles
     */
    public function createFromConfigs(array $configFiles) : \ECSPrefix20211214\Psr\Container\ContainerInterface;
    public function getContainer() : \ECSPrefix20211214\Psr\Container\ContainerInterface;
}
