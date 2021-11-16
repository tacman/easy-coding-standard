<?php

declare (strict_types=1);
/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */
namespace PhpCsFixer\Doctrine\Annotation;

use ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer;
use PhpCsFixer\Preg;
use PhpCsFixer\Tokenizer\Token as PhpToken;
/**
 * A list of Doctrine annotation tokens.
 *
 * @internal
 */
final class Tokens extends \SplFixedArray
{
    /**
     * @param string[] $ignoredTags
     *
     * @throws \InvalidArgumentException
     * @param PhpToken $input
     */
    public static function createFromDocComment($input, $ignoredTags = []) : self
    {
        if (!$input->isGivenKind(\T_DOC_COMMENT)) {
            throw new \InvalidArgumentException('Input must be a T_DOC_COMMENT token.');
        }
        $tokens = new self();
        $content = $input->getContent();
        $ignoredTextPosition = 0;
        $currentPosition = 0;
        $token = null;
        while (\false !== ($nextAtPosition = \strpos($content, '@', $currentPosition))) {
            if (0 !== $nextAtPosition && !\PhpCsFixer\Preg::match('/\\s/', $content[$nextAtPosition - 1])) {
                $currentPosition = $nextAtPosition + 1;
                continue;
            }
            $lexer = new \ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer();
            $lexer->setInput(\substr($content, $nextAtPosition));
            $scannedTokens = [];
            $index = 0;
            $nbScannedTokensToUse = 0;
            $nbScopes = 0;
            while (null !== ($token = $lexer->peek())) {
                if (0 === $index && \ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_AT !== $token['type']) {
                    break;
                }
                if (1 === $index) {
                    if (\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_IDENTIFIER !== $token['type'] || \in_array($token['value'], $ignoredTags, \true)) {
                        break;
                    }
                    $nbScannedTokensToUse = 2;
                }
                if ($index >= 2 && 0 === $nbScopes && !\in_array($token['type'], [\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_NONE, \ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS], \true)) {
                    break;
                }
                $scannedTokens[] = $token;
                if (\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS === $token['type']) {
                    ++$nbScopes;
                } elseif (\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS === $token['type']) {
                    if (0 === --$nbScopes) {
                        $nbScannedTokensToUse = \count($scannedTokens);
                        break;
                    }
                }
                ++$index;
            }
            if (0 !== $nbScopes) {
                break;
            }
            if (0 !== $nbScannedTokensToUse) {
                $ignoredTextLength = $nextAtPosition - $ignoredTextPosition;
                if (0 !== $ignoredTextLength) {
                    $tokens[] = new \PhpCsFixer\Doctrine\Annotation\Token(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_NONE, \substr($content, $ignoredTextPosition, $ignoredTextLength));
                }
                $lastTokenEndIndex = 0;
                foreach (\array_slice($scannedTokens, 0, $nbScannedTokensToUse) as $token) {
                    if (\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_STRING === $token['type']) {
                        $token['value'] = '"' . \str_replace('"', '""', $token['value']) . '"';
                    }
                    $missingTextLength = $token['position'] - $lastTokenEndIndex;
                    if ($missingTextLength > 0) {
                        $tokens[] = new \PhpCsFixer\Doctrine\Annotation\Token(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_NONE, \substr($content, $nextAtPosition + $lastTokenEndIndex, $missingTextLength));
                    }
                    $tokens[] = new \PhpCsFixer\Doctrine\Annotation\Token($token['type'], $token['value']);
                    $lastTokenEndIndex = $token['position'] + \strlen($token['value']);
                }
                $currentPosition = $ignoredTextPosition = $nextAtPosition + $token['position'] + \strlen($token['value']);
            } else {
                $currentPosition = $nextAtPosition + 1;
            }
        }
        if ($ignoredTextPosition < \strlen($content)) {
            $tokens[] = new \PhpCsFixer\Doctrine\Annotation\Token(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_NONE, \substr($content, $ignoredTextPosition));
        }
        return $tokens;
    }
    /**
     * Returns the index of the closest next token that is neither a comment nor a whitespace token.
     * @param int $index
     */
    public function getNextMeaningfulToken($index) : ?int
    {
        return $this->getMeaningfulTokenSibling($index, 1);
    }
    /**
     * Returns the index of the closest previous token that is neither a comment nor a whitespace token.
     * @param int $index
     */
    public function getPreviousMeaningfulToken($index) : ?int
    {
        return $this->getMeaningfulTokenSibling($index, -1);
    }
    /**
     * Returns the index of the closest next token of the given type.
     *
     * @param string|string[] $type
     * @param int $index
     */
    public function getNextTokenOfType($type, $index) : ?int
    {
        return $this->getTokenOfTypeSibling($index, $type, 1);
    }
    /**
     * Returns the index of the closest previous token of the given type.
     *
     * @param string|string[] $type
     * @param int $index
     */
    public function getPreviousTokenOfType($type, $index) : ?int
    {
        return $this->getTokenOfTypeSibling($index, $type, -1);
    }
    /**
     * Returns the index of the last token that is part of the annotation at the given index.
     * @param int $index
     */
    public function getAnnotationEnd($index) : ?int
    {
        $currentIndex = null;
        if (isset($this[$index + 2])) {
            if ($this[$index + 2]->isType(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS)) {
                $currentIndex = $index + 2;
            } elseif (isset($this[$index + 3]) && $this[$index + 2]->isType(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_NONE) && $this[$index + 3]->isType(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS) && \PhpCsFixer\Preg::match('/^(\\R\\s*\\*\\s*)*\\s*$/', $this[$index + 2]->getContent())) {
                $currentIndex = $index + 3;
            }
        }
        if (null !== $currentIndex) {
            $level = 0;
            for ($max = \count($this); $currentIndex < $max; ++$currentIndex) {
                if ($this[$currentIndex]->isType(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_OPEN_PARENTHESIS)) {
                    ++$level;
                } elseif ($this[$currentIndex]->isType(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_CLOSE_PARENTHESIS)) {
                    --$level;
                }
                if (0 === $level) {
                    return $currentIndex;
                }
            }
            return null;
        }
        return $index + 1;
    }
    /**
     * Returns the index of the close brace that matches the open brace at the given index.
     * @param int $index
     */
    public function getArrayEnd($index) : ?int
    {
        $level = 1;
        for (++$index, $max = \count($this); $index < $max; ++$index) {
            if ($this[$index]->isType(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_OPEN_CURLY_BRACES)) {
                ++$level;
            } elseif ($this[$index]->isType($index, \ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_CLOSE_CURLY_BRACES)) {
                --$level;
            }
            if (0 === $level) {
                return $index;
            }
        }
        return null;
    }
    /**
     * Returns the code from the tokens.
     */
    public function getCode() : string
    {
        $code = '';
        foreach ($this as $token) {
            $code .= $token->getContent();
        }
        return $code;
    }
    /**
     * Inserts a token at the given index.
     * @param int $index
     * @param \PhpCsFixer\Doctrine\Annotation\Token $token
     */
    public function insertAt($index, $token) : void
    {
        $this->setSize($this->getSize() + 1);
        for ($i = $this->getSize() - 1; $i > $index; --$i) {
            $this[$i] = $this[$i - 1] ?? new \PhpCsFixer\Doctrine\Annotation\Token();
        }
        $this[$index] = $token;
    }
    /**
     * {@inheritdoc}
     *
     * @throws \InvalidArgumentException
     */
    public function offsetSet($index, $token) : void
    {
        if (!$token instanceof \PhpCsFixer\Doctrine\Annotation\Token) {
            $type = \gettype($token);
            if ('object' === $type) {
                $type = \get_class($token);
            }
            throw new \InvalidArgumentException(\sprintf('Token must be an instance of PhpCsFixer\\Doctrine\\Annotation\\Token, %s given.', $type));
        }
        if (null === $index) {
            $index = \count($this);
            $this->setSize($this->getSize() + 1);
        }
        parent::offsetSet($index, $token);
    }
    /**
     * {@inheritdoc}
     *
     * @throws \OutOfBoundsException
     */
    public function offsetUnset($index) : void
    {
        if (!isset($this[$index])) {
            throw new \OutOfBoundsException(\sprintf('Index "%s" is invalid or does not exist.', $index));
        }
        $max = \count($this) - 1;
        while ($index < $max) {
            $this[$index] = $this[$index + 1];
            ++$index;
        }
        parent::offsetUnset($index);
        $this->setSize($max);
    }
    private function getMeaningfulTokenSibling(int $index, int $direction) : ?int
    {
        while (\true) {
            $index += $direction;
            if (!$this->offsetExists($index)) {
                break;
            }
            if (!$this[$index]->isType(\ECSPrefix20211116\Doctrine\Common\Annotations\DocLexer::T_NONE)) {
                return $index;
            }
        }
        return null;
    }
    /**
     * @param string|string[] $type
     */
    private function getTokenOfTypeSibling(int $index, $type, int $direction) : ?int
    {
        while (\true) {
            $index += $direction;
            if (!$this->offsetExists($index)) {
                break;
            }
            if ($this[$index]->isType($type)) {
                return $index;
            }
        }
        return null;
    }
}
