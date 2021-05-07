<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Fixer\FunctionNotation;

use PhpCsFixer\AbstractFixer;
use PhpCsFixer\DocBlock\Annotation;
use PhpCsFixer\DocBlock\DocBlock;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Token;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * @author Mark Nielsen
 */
final class VoidReturnFixer extends \PhpCsFixer\AbstractFixer
{
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Add `void` return type to functions with missing or empty return statements, but priority is given to `@return` annotations. Requires PHP >= 7.1.', [new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample("<?php\nfunction foo(\$a) {};\n", new \PhpCsFixer\FixerDefinition\VersionSpecification(70100))], null, 'Modifies the signature of functions.');
    }
    /**
     * {@inheritdoc}
     *
     * Must run before PhpdocNoEmptyReturnFixer, ReturnTypeDeclarationFixer.
     * Must run after NoSuperfluousPhpdocTagsFixer, SimplifiedNullReturnFixer.
     * @return int
     */
    public function getPriority()
    {
        return 5;
    }
    /**
     * {@inheritdoc}
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    public function isCandidate($tokens)
    {
        return \PHP_VERSION_ID >= 70100 && $tokens->isTokenKindFound(\T_FUNCTION);
    }
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isRisky()
    {
        return \true;
    }
    /**
     * {@inheritdoc}
     * @return void
     * @param \SplFileInfo $file
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    protected function applyFix($file, $tokens)
    {
        // These cause syntax errors.
        static $excludeFuncNames = [[\T_STRING, '__construct'], [\T_STRING, '__destruct'], [\T_STRING, '__clone']];
        for ($index = $tokens->count() - 1; 0 <= $index; --$index) {
            if (!$tokens[$index]->isGivenKind(\T_FUNCTION)) {
                continue;
            }
            $funcName = $tokens->getNextMeaningfulToken($index);
            if ($tokens[$funcName]->equalsAny($excludeFuncNames, \false)) {
                continue;
            }
            $startIndex = $tokens->getNextTokenOfKind($index, ['{', ';']);
            if ($this->hasReturnTypeHint($tokens, $startIndex)) {
                continue;
            }
            if ($tokens[$startIndex]->equals(';')) {
                // No function body defined, fallback to PHPDoc.
                if ($this->hasVoidReturnAnnotation($tokens, $index)) {
                    $this->fixFunctionDefinition($tokens, $startIndex);
                }
                continue;
            }
            if ($this->hasReturnAnnotation($tokens, $index)) {
                continue;
            }
            $endIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $startIndex);
            if ($this->hasVoidReturn($tokens, $startIndex, $endIndex)) {
                $this->fixFunctionDefinition($tokens, $startIndex);
            }
        }
    }
    /**
     * Determine whether there is a non-void return annotation in the function's PHPDoc comment.
     *
     * @param int $index The index of the function token
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    private function hasReturnAnnotation($tokens, $index)
    {
        foreach ($this->findReturnAnnotations($tokens, $index) as $return) {
            if (['void'] !== $return->getTypes()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Determine whether there is a void return annotation in the function's PHPDoc comment.
     *
     * @param int $index The index of the function token
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    private function hasVoidReturnAnnotation($tokens, $index)
    {
        foreach ($this->findReturnAnnotations($tokens, $index) as $return) {
            if (['void'] === $return->getTypes()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * Determine whether the function already has a return type hint.
     *
     * @param int $index The index of the end of the function definition line, EG at { or ;
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    private function hasReturnTypeHint($tokens, $index)
    {
        $endFuncIndex = $tokens->getPrevTokenOfKind($index, [')']);
        $nextIndex = $tokens->getNextMeaningfulToken($endFuncIndex);
        return $tokens[$nextIndex]->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON);
    }
    /**
     * Determine whether the function has a void return.
     *
     * @param int $startIndex Start of function body
     * @param int $endIndex   End of function body
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     * @return bool
     */
    private function hasVoidReturn($tokens, $startIndex, $endIndex)
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($i = $startIndex; $i < $endIndex; ++$i) {
            if ($tokens[$i]->isGivenKind(\T_CLASS) && $tokensAnalyzer->isAnonymousClass($i) || $tokens[$i]->isGivenKind(\T_FUNCTION) && $tokensAnalyzer->isLambda($i)) {
                $i = $tokens->getNextTokenOfKind($i, ['{']);
                $i = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_CURLY_BRACE, $i);
                continue;
            }
            if ($tokens[$i]->isGivenKind([\T_YIELD, \T_YIELD_FROM])) {
                return \false;
                // Generators cannot return void.
            }
            if (!$tokens[$i]->isGivenKind(\T_RETURN)) {
                continue;
            }
            $i = $tokens->getNextMeaningfulToken($i);
            if (!$tokens[$i]->equals(';')) {
                return \false;
            }
        }
        return \true;
    }
    /**
     * @param int $index The index of the end of the function definition line, EG at { or ;
     * @return void
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    private function fixFunctionDefinition($tokens, $index)
    {
        $endFuncIndex = $tokens->getPrevTokenOfKind($index, [')']);
        $tokens->insertAt($endFuncIndex + 1, [new \PhpCsFixer\Tokenizer\Token([\PhpCsFixer\Tokenizer\CT::T_TYPE_COLON, ':']), new \PhpCsFixer\Tokenizer\Token([\T_WHITESPACE, ' ']), new \PhpCsFixer\Tokenizer\Token([\T_STRING, 'void'])]);
    }
    /**
     * Find all the return annotations in the function's PHPDoc comment.
     *
     * @param int $index The index of the function token
     *
     * @return mixed[]
     * @param \PhpCsFixer\Tokenizer\Tokens $tokens
     */
    private function findReturnAnnotations($tokens, $index)
    {
        do {
            $index = $tokens->getPrevNonWhitespace($index);
        } while ($tokens[$index]->isGivenKind([\T_ABSTRACT, \T_FINAL, \T_PRIVATE, \T_PROTECTED, \T_PUBLIC, \T_STATIC]));
        if (!$tokens[$index]->isGivenKind(\T_DOC_COMMENT)) {
            return [];
        }
        $doc = new \PhpCsFixer\DocBlock\DocBlock($tokens[$index]->getContent());
        return $doc->getAnnotationsOfType('return');
    }
}
