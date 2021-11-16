<?php

declare (strict_types=1);
namespace Symplify\EasyCodingStandard\Console\Command;

use ECSPrefix20211116\Clue\React\NDJson\Decoder;
use ECSPrefix20211116\Clue\React\NDJson\Encoder;
use ECSPrefix20211116\React\EventLoop\StreamSelectLoop;
use ECSPrefix20211116\React\Socket\ConnectionInterface;
use ECSPrefix20211116\React\Socket\TcpConnector;
use ECSPrefix20211116\Symfony\Component\Console\Input\InputInterface;
use ECSPrefix20211116\Symfony\Component\Console\Output\OutputInterface;
use Symplify\EasyCodingStandard\Parallel\Enum\Action;
use Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand;
use Symplify\EasyCodingStandard\Parallel\WorkerRunner;
/**
 * Inspired at: https://github.com/phpstan/phpstan-src/commit/9124c66dcc55a222e21b1717ba5f60771f7dda92
 * https://github.com/phpstan/phpstan-src/blob/c471c7b050e0929daf432288770de673b394a983/src/Command/WorkerCommand.php
 *
 * ↓↓↓
 * https://github.com/phpstan/phpstan-src/commit/b84acd2e3eadf66189a64fdbc6dd18ff76323f67#diff-7f625777f1ce5384046df08abffd6c911cfbb1cfc8fcb2bdeaf78f337689e3e2
 */
final class WorkerCommand extends \Symplify\EasyCodingStandard\Console\Command\AbstractCheckCommand
{
    /**
     * @var \Symplify\EasyCodingStandard\Parallel\WorkerRunner
     */
    private $workerRunner;
    public function __construct(\Symplify\EasyCodingStandard\Parallel\WorkerRunner $workerRunner)
    {
        $this->workerRunner = $workerRunner;
        parent::__construct();
    }
    protected function configure() : void
    {
        parent::configure();
        $this->setDescription('(Internal) Support for parallel process');
    }
    /**
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute($input, $output) : int
    {
        $configuration = $this->configurationFactory->createFromInput($input);
        $streamSelectLoop = new \ECSPrefix20211116\React\EventLoop\StreamSelectLoop();
        $parallelIdentifier = $configuration->getParallelIdentifier();
        $tcpConnector = new \ECSPrefix20211116\React\Socket\TcpConnector($streamSelectLoop);
        $promise = $tcpConnector->connect('127.0.0.1:' . $configuration->getParallelPort());
        $promise->then(function (\ECSPrefix20211116\React\Socket\ConnectionInterface $connection) use($parallelIdentifier, $configuration) : void {
            $inDecoder = new \ECSPrefix20211116\Clue\React\NDJson\Decoder($connection, \true, 512, \JSON_INVALID_UTF8_IGNORE);
            $outEncoder = new \ECSPrefix20211116\Clue\React\NDJson\Encoder($connection, \JSON_INVALID_UTF8_IGNORE);
            // handshake?
            $outEncoder->write([\Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::ACTION => \Symplify\EasyCodingStandard\Parallel\Enum\Action::HELLO, \Symplify\EasyCodingStandard\Parallel\ValueObject\ReactCommand::IDENTIFIER => $parallelIdentifier]);
            $this->workerRunner->run($outEncoder, $inDecoder, $configuration);
        });
        $streamSelectLoop->run();
        return self::SUCCESS;
    }
}
