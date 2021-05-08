<?php

/*
 * This file is part of sebastian/diff.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210508\SebastianBergmann\Diff\Output;

use function fclose;
use function fopen;
use function fwrite;
use function stream_get_contents;
use function substr;
use ECSPrefix20210508\SebastianBergmann\Diff\Differ;
/**
 * Builds a diff string representation in a loose unified diff format
 * listing only changes lines. Does not include line numbers.
 */
final class DiffOnlyOutputBuilder implements \ECSPrefix20210508\SebastianBergmann\Diff\Output\DiffOutputBuilderInterface
{
    /**
     * @var string
     */
    private $header;
    /**
     * @param string $header
     */
    public function __construct($header = "--- Original\n+++ New\n")
    {
        $header = (string) $header;
        $this->header = $header;
    }
    /**
     * @return string
     */
    public function getDiff(array $diff)
    {
        $buffer = \fopen('php://memory', 'r+b');
        if ('' !== $this->header) {
            \fwrite($buffer, $this->header);
            if ("\n" !== \substr($this->header, -1, 1)) {
                \fwrite($buffer, "\n");
            }
        }
        foreach ($diff as $diffEntry) {
            if ($diffEntry[1] === \ECSPrefix20210508\SebastianBergmann\Diff\Differ::ADDED) {
                \fwrite($buffer, '+' . $diffEntry[0]);
            } elseif ($diffEntry[1] === \ECSPrefix20210508\SebastianBergmann\Diff\Differ::REMOVED) {
                \fwrite($buffer, '-' . $diffEntry[0]);
            } elseif ($diffEntry[1] === \ECSPrefix20210508\SebastianBergmann\Diff\Differ::DIFF_LINE_END_WARNING) {
                \fwrite($buffer, ' ' . $diffEntry[0]);
                continue;
                // Warnings should not be tested for line break, it will always be there
            } else {
                /* Not changed (old) 0 */
                continue;
                // we didn't write the non changs line, so do not add a line break either
            }
            $lc = \substr($diffEntry[0], -1);
            if ($lc !== "\n" && $lc !== "\r") {
                \fwrite($buffer, "\n");
                // \No newline at end of file
            }
        }
        $diff = \stream_get_contents($buffer, -1, 0);
        \fclose($buffer);
        return $diff;
    }
}
