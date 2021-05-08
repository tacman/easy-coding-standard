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
use PhpCsFixer\Fixer\ConfigurableFixerInterface;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolver;
use PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface;
use PhpCsFixer\FixerConfiguration\FixerOptionBuilder;
use PhpCsFixer\FixerDefinition\CodeSample;
use PhpCsFixer\FixerDefinition\FixerDefinition;
use PhpCsFixer\FixerDefinition\FixerDefinitionInterface;
use PhpCsFixer\FixerDefinition\VersionSpecification;
use PhpCsFixer\FixerDefinition\VersionSpecificCodeSample;
use PhpCsFixer\Tokenizer\CT;
use PhpCsFixer\Tokenizer\Tokens;
use PhpCsFixer\Tokenizer\TokensAnalyzer;
/**
 * Fixer for rules defined in PSR2 generally (¶1 and ¶6).
 *
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class FunctionDeclarationFixer extends \PhpCsFixer\AbstractFixer implements \PhpCsFixer\Fixer\ConfigurableFixerInterface
{
    /**
     * @internal
     */
    const SPACING_NONE = 'none';
    /**
     * @internal
     */
    const SPACING_ONE = 'one';
    const SUPPORTED_SPACINGS = [self::SPACING_NONE, self::SPACING_ONE];
    private $singleLineWhitespaceOptions = " \t";
    /**
     * {@inheritdoc}
     * @return bool
     */
    public function isCandidate(\PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        if (\PHP_VERSION_ID >= 70400 && $tokens->isTokenKindFound(\T_FN)) {
            return \true;
        }
        return $tokens->isTokenKindFound(\T_FUNCTION);
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerDefinition\FixerDefinitionInterface
     */
    public function getDefinition()
    {
        return new \PhpCsFixer\FixerDefinition\FixerDefinition('Spaces should be properly placed in a function declaration.', [new \PhpCsFixer\FixerDefinition\CodeSample('<?php

class Foo
{
    public static function  bar   ( $baz , $foo )
    {
        return false;
    }
}

function  foo  ($bar, $baz)
{
    return false;
}
'), new \PhpCsFixer\FixerDefinition\CodeSample('<?php
$f = function () {};
', ['closure_function_spacing' => self::SPACING_NONE]), new \PhpCsFixer\FixerDefinition\VersionSpecificCodeSample('<?php
$f = fn () => null;
', new \PhpCsFixer\FixerDefinition\VersionSpecification(70400), ['closure_function_spacing' => self::SPACING_NONE])]);
    }
    /**
     * {@inheritdoc}
     *
     * Must run before MethodArgumentSpaceFixer.
     * Must run after SingleSpaceAfterConstructFixer.
     * @return int
     */
    public function getPriority()
    {
        return 31;
    }
    /**
     * {@inheritdoc}
     * @return void
     */
    protected function applyFix(\SplFileInfo $file, \PhpCsFixer\Tokenizer\Tokens $tokens)
    {
        $tokensAnalyzer = new \PhpCsFixer\Tokenizer\TokensAnalyzer($tokens);
        for ($index = $tokens->count() - 1; $index >= 0; --$index) {
            $token = $tokens[$index];
            if (!$token->isGivenKind(\T_FUNCTION) && (\PHP_VERSION_ID < 70400 || !$token->isGivenKind(\T_FN))) {
                continue;
            }
            $startParenthesisIndex = $tokens->getNextTokenOfKind($index, ['(', ';', [\T_CLOSE_TAG]]);
            if (!$tokens[$startParenthesisIndex]->equals('(')) {
                continue;
            }
            $endParenthesisIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $startParenthesisIndex);
            $startBraceIndex = $tokens->getNextTokenOfKind($endParenthesisIndex, [';', '{', [\T_DOUBLE_ARROW]]);
            // fix single-line whitespace before { or =>
            // eg: `function foo(){}` => `function foo() {}`
            // eg: `function foo()   {}` => `function foo() {}`
            // eg: `fn()   =>` => `fn() =>`
            if ($tokens[$startBraceIndex]->equalsAny(['{', [\T_DOUBLE_ARROW]]) && (!$tokens[$startBraceIndex - 1]->isWhitespace() || $tokens[$startBraceIndex - 1]->isWhitespace($this->singleLineWhitespaceOptions))) {
                $tokens->ensureWhitespaceAtIndex($startBraceIndex - 1, 1, ' ');
            }
            $afterParenthesisIndex = $tokens->getNextNonWhitespace($endParenthesisIndex);
            $afterParenthesisToken = $tokens[$afterParenthesisIndex];
            if ($afterParenthesisToken->isGivenKind(\PhpCsFixer\Tokenizer\CT::T_USE_LAMBDA)) {
                // fix whitespace after CT:T_USE_LAMBDA (we might add a token, so do this before determining start and end parenthesis)
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex + 1, 0, ' ');
                $useStartParenthesisIndex = $tokens->getNextTokenOfKind($afterParenthesisIndex, ['(']);
                $useEndParenthesisIndex = $tokens->findBlockEnd(\PhpCsFixer\Tokenizer\Tokens::BLOCK_TYPE_PARENTHESIS_BRACE, $useStartParenthesisIndex);
                // remove single-line edge whitespaces inside use parentheses
                $this->fixParenthesisInnerEdge($tokens, $useStartParenthesisIndex, $useEndParenthesisIndex);
                // fix whitespace before CT::T_USE_LAMBDA
                $tokens->ensureWhitespaceAtIndex($afterParenthesisIndex - 1, 1, ' ');
            }
            // remove single-line edge whitespaces inside parameters list parentheses
            $this->fixParenthesisInnerEdge($tokens, $startParenthesisIndex, $endParenthesisIndex);
            $isLambda = $tokensAnalyzer->isLambda($index);
            // remove whitespace before (
            // eg: `function foo () {}` => `function foo() {}`
            if (!$isLambda && $tokens[$startParenthesisIndex - 1]->isWhitespace() && !$tokens[$tokens->getPrevNonWhitespace($startParenthesisIndex - 1)]->isComment()) {
                $tokens->clearAt($startParenthesisIndex - 1);
            }
            if ($isLambda && self::SPACING_NONE === $this->configuration['closure_function_spacing']) {
                // optionally remove whitespace after T_FUNCTION of a closure
                // eg: `function () {}` => `function() {}`
                if ($tokens[$index + 1]->isWhitespace()) {
                    $tokens->clearAt($index + 1);
                }
            } else {
                // otherwise, enforce whitespace after T_FUNCTION
                // eg: `function     foo() {}` => `function foo() {}`
                $tokens->ensureWhitespaceAtIndex($index + 1, 0, ' ');
            }
            if ($isLambda) {
                $prev = $tokens->getPrevMeaningfulToken($index);
                if ($tokens[$prev]->isGivenKind(\T_STATIC)) {
                    // fix whitespace after T_STATIC
                    // eg: `$a = static     function(){};` => `$a = static function(){};`
                    $tokens->ensureWhitespaceAtIndex($prev + 1, 0, ' ');
                }
            }
        }
    }
    /**
     * {@inheritdoc}
     * @return \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
     */
    protected function createConfigurationDefinition()
    {
        return new \PhpCsFixer\FixerConfiguration\FixerConfigurationResolver([(new \PhpCsFixer\FixerConfiguration\FixerOptionBuilder('closure_function_spacing', 'Spacing to use before open parenthesis for closures.'))->setDefault(self::SPACING_ONE)->setAllowedValues(self::SUPPORTED_SPACINGS)->getOption()]);
    }
    /**
     * @return void
     * @param int $start
     * @param int $end
     */
    private function fixParenthesisInnerEdge(\PhpCsFixer\Tokenizer\Tokens $tokens, $start, $end)
    {
        $start = (int) $start;
        $end = (int) $end;
        // remove single-line whitespace before )
        if ($tokens[$end - 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens->clearAt($end - 1);
        }
        // remove single-line whitespace after (
        if ($tokens[$start + 1]->isWhitespace($this->singleLineWhitespaceOptions)) {
            $tokens->clearAt($start + 1);
        }
    }
}
