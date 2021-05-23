<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Style;

use ECSPrefix20210523\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20210523\Symfony\Component\Console\Output\OutputInterface;
use ECSPrefix20210523\Symfony\Component\Console\Style\SymfonyStyle;
use ECSPrefix20210523\Symfony\Component\Console\Terminal;
use Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError;
use ECSPrefix20210523\Symplify\PackageBuilder\Reflection\PrivatesAccessor;
use ECSPrefix20210523\Symplify\PackageBuilder\Reflection\PrivatesCaller;
final class EasyCodingStandardStyle extends \ECSPrefix20210523\Symfony\Component\Console\Style\SymfonyStyle
{
    /**
     * To fit in Linux/Windows terminal windows to prevent overflow.
     *
     * @var int
     */
    const BULGARIAN_CONSTANT = 8;
    /**
     * @var Terminal
     */
    private $terminal;
    public function __construct(\ECSPrefix20210523\Symfony\Component\Console\Input\InputInterface $input, \ECSPrefix20210523\Symfony\Component\Console\Output\OutputInterface $output, \ECSPrefix20210523\Symfony\Component\Console\Terminal $terminal)
    {
        parent::__construct($input, $output);
        $this->terminal = $terminal;
    }
    /**
     * @param CodingStandardError[] $codingStandardErrors
     * @return void
     */
    public function printErrors(array $codingStandardErrors)
    {
        /** @var CodingStandardError $codingStandardError */
        foreach ($codingStandardErrors as $codingStandardError) {
            $this->separator();
            $this->writeln(' ' . $codingStandardError->getFileWithLine());
            $this->separator();
            $message = $this->createMessageFromFileError($codingStandardError);
            $this->writeln(' ' . $message);
            $this->separator();
            $this->newLine();
        }
    }
    /**
     * @return void
     */
    public function enableDebugProgressBar()
    {
        $privatesAccessor = new \ECSPrefix20210523\Symplify\PackageBuilder\Reflection\PrivatesAccessor();
        $progressBar = $privatesAccessor->getPrivateProperty($this, 'progressBar');
        $privatesCaller = new \ECSPrefix20210523\Symplify\PackageBuilder\Reflection\PrivatesCaller();
        $privatesCaller->callPrivateMethod($progressBar, 'setRealFormat', ['debug']);
    }
    /**
     * @return void
     */
    private function separator()
    {
        $separator = \str_repeat('-', $this->getTerminalWidth());
        $this->writeln(' ' . $separator);
    }
    private function createMessageFromFileError(\Symplify\EasyCodingStandard\ValueObject\Error\CodingStandardError $codingStandardError) : string
    {
        $message = \sprintf('%s%s Reported by: "%s"', $codingStandardError->getMessage(), \PHP_EOL . \PHP_EOL, $codingStandardError->getCheckerClass());
        $message = $this->clearCrLfFromMessage($message);
        return $this->wrapMessageSoItFitsTheColumnWidth($message);
    }
    private function getTerminalWidth() : int
    {
        return $this->terminal->getWidth() - self::BULGARIAN_CONSTANT;
    }
    /**
     * This prevents message override in Windows system.
     */
    private function clearCrLfFromMessage(string $message) : string
    {
        return \str_replace("\r", '', $message);
    }
    private function wrapMessageSoItFitsTheColumnWidth(string $message) : string
    {
        return \wordwrap($message, $this->getTerminalWidth(), \PHP_EOL);
    }
}
