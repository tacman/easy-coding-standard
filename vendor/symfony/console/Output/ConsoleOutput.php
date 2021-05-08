<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\Symfony\Component\Console\Output;

use ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatterInterface;
/**
 * ConsoleOutput is the default class for all CLI output. It uses STDOUT and STDERR.
 *
 * This class is a convenient wrapper around `StreamOutput` for both STDOUT and STDERR.
 *
 *     $output = new ConsoleOutput();
 *
 * This is equivalent to:
 *
 *     $output = new StreamOutput(fopen('php://stdout', 'w'));
 *     $stdErr = new StreamOutput(fopen('php://stderr', 'w'));
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
class ConsoleOutput extends \ECSPrefix20210508\Symfony\Component\Console\Output\StreamOutput implements \ECSPrefix20210508\Symfony\Component\Console\Output\ConsoleOutputInterface
{
    private $stderr;
    private $consoleSectionOutputs = [];
    /**
     * @param int                           $verbosity The verbosity level (one of the VERBOSITY constants in OutputInterface)
     * @param bool $decorated Whether to decorate messages (null for auto-guessing)
     * @param OutputFormatterInterface|null $formatter Output formatter instance (null to use default OutputFormatter)
     */
    public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, \ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter = null)
    {
        $verbosity = (int) $verbosity;
        parent::__construct($this->openOutputStream(), $verbosity, $decorated, $formatter);
        if (null === $formatter) {
            // for BC reasons, stdErr has it own Formatter only when user don't inject a specific formatter.
            $this->stderr = new \ECSPrefix20210508\Symfony\Component\Console\Output\StreamOutput($this->openErrorStream(), $verbosity, $decorated);
            return;
        }
        $actualDecorated = $this->isDecorated();
        $this->stderr = new \ECSPrefix20210508\Symfony\Component\Console\Output\StreamOutput($this->openErrorStream(), $verbosity, $decorated, $this->getFormatter());
        if (null === $decorated) {
            $this->setDecorated($actualDecorated && $this->stderr->isDecorated());
        }
    }
    /**
     * Creates a new output section.
     * @return \Symfony\Component\Console\Output\ConsoleSectionOutput
     */
    public function section()
    {
        return new \ECSPrefix20210508\Symfony\Component\Console\Output\ConsoleSectionOutput($this->getStream(), $this->consoleSectionOutputs, $this->getVerbosity(), $this->isDecorated(), $this->getFormatter());
    }
    /**
     * {@inheritdoc}
     * @param bool $decorated
     */
    public function setDecorated($decorated)
    {
        $decorated = (bool) $decorated;
        parent::setDecorated($decorated);
        $this->stderr->setDecorated($decorated);
    }
    /**
     * {@inheritdoc}
     */
    public function setFormatter(\ECSPrefix20210508\Symfony\Component\Console\Formatter\OutputFormatterInterface $formatter)
    {
        parent::setFormatter($formatter);
        $this->stderr->setFormatter($formatter);
    }
    /**
     * {@inheritdoc}
     * @param int $level
     */
    public function setVerbosity($level)
    {
        $level = (int) $level;
        parent::setVerbosity($level);
        $this->stderr->setVerbosity($level);
    }
    /**
     * {@inheritdoc}
     */
    public function getErrorOutput()
    {
        return $this->stderr;
    }
    /**
     * {@inheritdoc}
     */
    public function setErrorOutput(\ECSPrefix20210508\Symfony\Component\Console\Output\OutputInterface $error)
    {
        $this->stderr = $error;
    }
    /**
     * Returns true if current environment supports writing console output to
     * STDOUT.
     *
     * @return bool
     */
    protected function hasStdoutSupport()
    {
        return \false === $this->isRunningOS400();
    }
    /**
     * Returns true if current environment supports writing console output to
     * STDERR.
     *
     * @return bool
     */
    protected function hasStderrSupport()
    {
        return \false === $this->isRunningOS400();
    }
    /**
     * Checks if current executing environment is IBM iSeries (OS400), which
     * doesn't properly convert character-encodings between ASCII to EBCDIC.
     * @return bool
     */
    private function isRunningOS400()
    {
        $checks = [\function_exists('php_uname') ? \php_uname('s') : '', \getenv('OSTYPE'), \PHP_OS];
        return \false !== \stripos(\implode(';', $checks), 'OS400');
    }
    /**
     * @return resource
     */
    private function openOutputStream()
    {
        if (!$this->hasStdoutSupport()) {
            return \fopen('php://output', 'w');
        }
        return @\fopen('php://stdout', 'w') ?: \fopen('php://output', 'w');
    }
    /**
     * @return resource
     */
    private function openErrorStream()
    {
        return \fopen($this->hasStderrSupport() ? 'php://stderr' : 'php://output', 'w');
    }
}
