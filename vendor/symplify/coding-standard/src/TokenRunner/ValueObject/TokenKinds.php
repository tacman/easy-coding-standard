<?php

namespace Symplify\CodingStandard\TokenRunner\ValueObject;

use PhpCsFixer\Tokenizer\CT;
final class TokenKinds
{
    /**
     * @var int[]
     */
    const ARRAY_OPEN_TOKENS = [\T_ARRAY, \PhpCsFixer\Tokenizer\CT::T_ARRAY_SQUARE_BRACE_OPEN];
}
