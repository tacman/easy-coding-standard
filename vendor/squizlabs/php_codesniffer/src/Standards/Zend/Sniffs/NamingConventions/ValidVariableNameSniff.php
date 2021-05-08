<?php

/**
 * Checks the naming of variables and member variables.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace PHP_CodeSniffer\Standards\Zend\Sniffs\NamingConventions;

use PHP_CodeSniffer\Sniffs\AbstractVariableSniff;
use PHP_CodeSniffer\Util\Common;
use PHP_CodeSniffer\Files\File;
use PHP_CodeSniffer\Util\Tokens;
class ValidVariableNameSniff extends \PHP_CodeSniffer\Sniffs\AbstractVariableSniff
{
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function processVariable(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $stackPtr = (int) $stackPtr;
        $tokens = $phpcsFile->getTokens();
        $varName = \ltrim($tokens[$stackPtr]['content'], '$');
        // If it's a php reserved var, then its ok.
        if (isset($this->phpReservedVars[$varName]) === \true) {
            return;
        }
        $objOperator = $phpcsFile->findNext([\T_WHITESPACE], $stackPtr + 1, null, \true);
        if ($tokens[$objOperator]['code'] === \T_OBJECT_OPERATOR || $tokens[$objOperator]['code'] === \T_NULLSAFE_OBJECT_OPERATOR) {
            // Check to see if we are using a variable from an object.
            $var = $phpcsFile->findNext([\T_WHITESPACE], $objOperator + 1, null, \true);
            if ($tokens[$var]['code'] === \T_STRING) {
                // Either a var name or a function call, so check for bracket.
                $bracket = $phpcsFile->findNext([\T_WHITESPACE], $var + 1, null, \true);
                if ($tokens[$bracket]['code'] !== T_OPEN_PARENTHESIS) {
                    $objVarName = $tokens[$var]['content'];
                    // There is no way for us to know if the var is public or private,
                    // so we have to ignore a leading underscore if there is one and just
                    // check the main part of the variable name.
                    $originalVarName = $objVarName;
                    if (\substr($objVarName, 0, 1) === '_') {
                        $objVarName = \substr($objVarName, 1);
                    }
                    if (\PHP_CodeSniffer\Util\Common::isCamelCaps($objVarName, \false, \true, \false) === \false) {
                        $error = 'Variable "%s" is not in valid camel caps format';
                        $data = [$originalVarName];
                        $phpcsFile->addError($error, $var, 'NotCamelCaps', $data);
                    } else {
                        if (\preg_match('|\\d|', $objVarName) === 1) {
                            $warning = 'Variable "%s" contains numbers but this is discouraged';
                            $data = [$originalVarName];
                            $phpcsFile->addWarning($warning, $stackPtr, 'ContainsNumbers', $data);
                        }
                    }
                }
                //end if
            }
            //end if
        }
        //end if
        // There is no way for us to know if the var is public or private,
        // so we have to ignore a leading underscore if there is one and just
        // check the main part of the variable name.
        $originalVarName = $varName;
        if (\substr($varName, 0, 1) === '_') {
            $objOperator = $phpcsFile->findPrevious([\T_WHITESPACE], $stackPtr - 1, null, \true);
            if ($tokens[$objOperator]['code'] === \T_DOUBLE_COLON) {
                // The variable lives within a class, and is referenced like
                // this: MyClass::$_variable, so we don't know its scope.
                $inClass = \true;
            } else {
                $inClass = $phpcsFile->hasCondition($stackPtr, \PHP_CodeSniffer\Util\Tokens::$ooScopeTokens);
            }
            if ($inClass === \true) {
                $varName = \substr($varName, 1);
            }
        }
        if (\PHP_CodeSniffer\Util\Common::isCamelCaps($varName, \false, \true, \false) === \false) {
            $error = 'Variable "%s" is not in valid camel caps format';
            $data = [$originalVarName];
            $phpcsFile->addError($error, $stackPtr, 'NotCamelCaps', $data);
        } else {
            if (\preg_match('|\\d|', $varName) === 1) {
                $warning = 'Variable "%s" contains numbers but this is discouraged';
                $data = [$originalVarName];
                $phpcsFile->addWarning($warning, $stackPtr, 'ContainsNumbers', $data);
            }
        }
    }
    //end processVariable()
    /**
     * Processes class member variables.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the current token in the
     *                                               stack passed in $tokens.
     *
     * @return void
     */
    protected function processMemberVar(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $stackPtr = (int) $stackPtr;
        $tokens = $phpcsFile->getTokens();
        $varName = \ltrim($tokens[$stackPtr]['content'], '$');
        $memberProps = $phpcsFile->getMemberProperties($stackPtr);
        if (empty($memberProps) === \true) {
            // Exception encountered.
            return;
        }
        $public = $memberProps['scope'] === 'public';
        if ($public === \true) {
            if (\substr($varName, 0, 1) === '_') {
                $error = 'Public member variable "%s" must not contain a leading underscore';
                $data = [$varName];
                $phpcsFile->addError($error, $stackPtr, 'PublicHasUnderscore', $data);
            }
        } else {
            if (\substr($varName, 0, 1) !== '_') {
                $scope = \ucfirst($memberProps['scope']);
                $error = '%s member variable "%s" must contain a leading underscore';
                $data = [$scope, $varName];
                $phpcsFile->addError($error, $stackPtr, 'PrivateNoUnderscore', $data);
            }
        }
        // Remove a potential underscore prefix for testing CamelCaps.
        $varName = \ltrim($varName, '_');
        if (\PHP_CodeSniffer\Util\Common::isCamelCaps($varName, \false, \true, \false) === \false) {
            $error = 'Member variable "%s" is not in valid camel caps format';
            $data = [$varName];
            $phpcsFile->addError($error, $stackPtr, 'MemberVarNotCamelCaps', $data);
        } else {
            if (\preg_match('|\\d|', $varName) === 1) {
                $warning = 'Member variable "%s" contains numbers but this is discouraged';
                $data = [$varName];
                $phpcsFile->addWarning($warning, $stackPtr, 'MemberVarContainsNumbers', $data);
            }
        }
    }
    //end processMemberVar()
    /**
     * Processes the variable found within a double quoted string.
     *
     * @param \PHP_CodeSniffer\Files\File $phpcsFile The file being scanned.
     * @param int                         $stackPtr  The position of the double quoted
     *                                               string.
     *
     * @return void
     */
    protected function processVariableInString(\PHP_CodeSniffer\Files\File $phpcsFile, $stackPtr)
    {
        $stackPtr = (int) $stackPtr;
        $tokens = $phpcsFile->getTokens();
        if (\preg_match_all('|[^\\\\]\\$([a-zA-Z_\\x7f-\\xff][a-zA-Z0-9_\\x7f-\\xff]*)|', $tokens[$stackPtr]['content'], $matches) !== 0) {
            foreach ($matches[1] as $varName) {
                // If it's a php reserved var, then its ok.
                if (isset($this->phpReservedVars[$varName]) === \true) {
                    continue;
                }
                if (\PHP_CodeSniffer\Util\Common::isCamelCaps($varName, \false, \true, \false) === \false) {
                    $error = 'Variable "%s" is not in valid camel caps format';
                    $data = [$varName];
                    $phpcsFile->addError($error, $stackPtr, 'StringVarNotCamelCaps', $data);
                } else {
                    if (\preg_match('|\\d|', $varName) === 1) {
                        $warning = 'Variable "%s" contains numbers but this is discouraged';
                        $data = [$varName];
                        $phpcsFile->addWarning($warning, $stackPtr, 'StringVarContainsNumbers', $data);
                    }
                }
            }
            //end foreach
        }
        //end if
    }
    //end processVariableInString()
}
//end class
