<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\DependencyInjection;

use ECSPrefix20210523\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210523\Symfony\Component\DependencyInjection\ContainerInterface;
use Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector;
use Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel;
use ECSPrefix20210523\Symplify\PackageBuilder\Console\Input\StaticInputDetector;
use ECSPrefix20210523\Symplify\SmartFileSystem\SmartFileInfo;
final class EasyCodingStandardContainerFactory
{
    public function createFromFromInput(\ECSPrefix20210523\Symfony\Component\Console\Input\InputInterface $input) : \ECSPrefix20210523\Symfony\Component\DependencyInjection\ContainerInterface
    {
        $environment = 'prod' . \random_int(1, 100000);
        $easyCodingStandardKernel = new \Symplify\EasyCodingStandard\HttpKernel\EasyCodingStandardKernel($environment, \ECSPrefix20210523\Symplify\PackageBuilder\Console\Input\StaticInputDetector::isDebug());
        $inputConfigFileInfos = [];
        $rootECSConfig = \getcwd() . \DIRECTORY_SEPARATOR . '/ecs.php';
        if ($input->hasParameterOption(['--config', '-c'])) {
            $commandLineConfigFile = $input->getParameterOption(['--config', '-c']);
            if (\is_string($commandLineConfigFile) && \file_exists($commandLineConfigFile)) {
                $inputConfigFileInfos[] = new \ECSPrefix20210523\Symplify\SmartFileSystem\SmartFileInfo($commandLineConfigFile);
            }
        } elseif (\file_exists($rootECSConfig)) {
            $inputConfigFileInfos[] = new \ECSPrefix20210523\Symplify\SmartFileSystem\SmartFileInfo($rootECSConfig);
        }
        if ($inputConfigFileInfos !== []) {
            $easyCodingStandardKernel->setConfigs($inputConfigFileInfos);
        }
        $easyCodingStandardKernel->boot();
        $container = $easyCodingStandardKernel->getContainer();
        if ($inputConfigFileInfos !== []) {
            // for cache invalidation on config change
            /** @var ChangedFilesDetector $changedFilesDetector */
            $changedFilesDetector = $container->get(\Symplify\EasyCodingStandard\ChangedFilesDetector\ChangedFilesDetector::class);
            $changedFilesDetector->setUsedConfigs($inputConfigFileInfos);
        }
        return $container;
    }
}
