<?php

declare (strict_types=1);
namespace ECSPrefix20210707\Symplify\ConsoleColorDiff\Bundle;

use ECSPrefix20210707\Symfony\Component\HttpKernel\Bundle\Bundle;
use ECSPrefix20210707\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension;
final class ConsoleColorDiffBundle extends \ECSPrefix20210707\Symfony\Component\HttpKernel\Bundle\Bundle
{
    /**
     * @return \Symfony\Component\DependencyInjection\Extension\ExtensionInterface|null
     */
    protected function createContainerExtension()
    {
        return new \ECSPrefix20210707\Symplify\ConsoleColorDiff\DependencyInjection\Extension\ConsoleColorDiffExtension();
    }
}
