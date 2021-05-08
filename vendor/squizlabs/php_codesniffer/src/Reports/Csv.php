<?php

/**
 * CSV report for PHP_CodeSniffer.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Reports;

use PHP_CodeSniffer\Files\File;
class Csv implements \PHP_CodeSniffer\Reports\Report
{
    /**
     * Generate a partial report for a single processed file.
     *
     * Function should return TRUE if it printed or stored data about the file
     * and FALSE if it ignored the file. Returning TRUE indicates that the file and
     * its data should be counted in the grand totals.
     *
     * @param array                 $report      Prepared report data.
     * @param \PHP_CodeSniffer\File $phpcsFile   The file being reported on.
     * @param bool                  $showSources Show sources?
     * @param int                   $width       Maximum allowed line width.
     *
     * @return bool
     */
    public function generateFileReport($report, \PHP_CodeSniffer\Files\File $phpcsFile, $showSources = \false, $width = 80)
    {
        $showSources = (bool) $showSources;
        $width = (int) $width;
        if ($report['errors'] === 0 && $report['warnings'] === 0) {
            // Nothing to print.
            return \false;
        }
        foreach ($report['messages'] as $line => $lineErrors) {
            foreach ($lineErrors as $column => $colErrors) {
                foreach ($colErrors as $error) {
                    $filename = \str_replace('"', '\\"', $report['filename']);
                    $message = \str_replace('"', '\\"', $error['message']);
                    $type = \strtolower($error['type']);
                    $source = $error['source'];
                    $severity = $error['severity'];
                    $fixable = (int) $error['fixable'];
                    echo "\"{$filename}\",{$line},{$column},{$type},\"{$message}\",{$source},{$severity},{$fixable}" . \PHP_EOL;
                }
            }
        }
        return \true;
    }
    //end generateFileReport()
    /**
     * Generates a csv report.
     *
     * @param string $cachedData    Any partial report data that was returned from
     *                              generateFileReport during the run.
     * @param int    $totalFiles    Total number of files processed during the run.
     * @param int    $totalErrors   Total number of errors found during the run.
     * @param int    $totalWarnings Total number of warnings found during the run.
     * @param int    $totalFixable  Total number of problems that can be fixed.
     * @param bool   $showSources   Show sources?
     * @param int    $width         Maximum allowed line width.
     * @param bool   $interactive   Are we running in interactive mode?
     * @param bool   $toScreen      Is the report being printed to screen?
     *
     * @return void
     */
    public function generate($cachedData, $totalFiles, $totalErrors, $totalWarnings, $totalFixable, $showSources = \false, $width = 80, $interactive = \false, $toScreen = \true)
    {
        $cachedData = (string) $cachedData;
        $totalFiles = (int) $totalFiles;
        $totalErrors = (int) $totalErrors;
        $totalWarnings = (int) $totalWarnings;
        $totalFixable = (int) $totalFixable;
        $showSources = (bool) $showSources;
        $width = (int) $width;
        $interactive = (bool) $interactive;
        $toScreen = (bool) $toScreen;
        echo 'File,Line,Column,Type,Message,Source,Severity,Fixable' . \PHP_EOL;
        echo $cachedData;
    }
    //end generate()
}
//end class
