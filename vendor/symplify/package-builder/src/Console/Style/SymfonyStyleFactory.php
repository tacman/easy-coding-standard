<?php

declare (strict_types=1);
namespace ECSPrefix202206\Symplify\PackageBuilder\Console\Style;

use ECSPrefix202206\Symfony\Component\Console\Application;
use ECSPrefix202206\Symfony\Component\Console\Input\ArgvInput;
use ECSPrefix202206\Symfony\Component\Console\Output\ConsoleOutput;
use ECSPrefix202206\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix202206\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix202206\Symplify\EasyTesting\PHPUnit\StaticPHPUnitEnvironment;
use ECSPrefix202206\Symplify\PackageBuilder\Reflection\PrivatesCaller;
/**
 * @api
 */
final class SymfonyStyleFactory
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\PrivatesCaller
     */
    private $privatesCaller;
    public function __construct()
    {
        $this->privatesCaller = new PrivatesCaller();
    }
    public function create() : SymfonyStyle
    {
        // to prevent missing argv indexes
        if (!isset($_SERVER['argv'])) {
            $_SERVER['argv'] = [];
        }
        $argvInput = new ArgvInput();
        $consoleOutput = new ConsoleOutput();
        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new Application(), 'configureIO', [$argvInput, $consoleOutput]);
        // --debug is called
        if ($argvInput->hasParameterOption('--debug')) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
        }
        // disable output for tests
        if (StaticPHPUnitEnvironment::isPHPUnitRun()) {
            $consoleOutput->setVerbosity(OutputInterface::VERBOSITY_QUIET);
        }
        return new SymfonyStyle($argvInput, $consoleOutput);
    }
}
