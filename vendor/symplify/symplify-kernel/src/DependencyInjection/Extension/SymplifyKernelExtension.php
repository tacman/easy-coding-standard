<?php

declare (strict_types=1);
namespace ECSPrefix20210517\Symplify\SymplifyKernel\DependencyInjection\Extension;

use ECSPrefix20210517\Symfony\Component\Config\FileLocator;
use ECSPrefix20210517\Symfony\Component\DependencyInjection\ContainerBuilder;
use ECSPrefix20210517\Symfony\Component\DependencyInjection\Extension\Extension;
use ECSPrefix20210517\Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
final class SymplifyKernelExtension extends \ECSPrefix20210517\Symfony\Component\DependencyInjection\Extension\Extension
{
    /**
     * @param string[] $configs
     * @return void
     */
    public function load(array $configs, \ECSPrefix20210517\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        $phpFileLoader = new \ECSPrefix20210517\Symfony\Component\DependencyInjection\Loader\PhpFileLoader($containerBuilder, new \ECSPrefix20210517\Symfony\Component\Config\FileLocator(__DIR__ . '/../../../config'));
        $phpFileLoader->load('common-config.php');
    }
}
