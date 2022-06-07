<?php

declare (strict_types=1);
namespace ECSPrefix20220607\Symplify\CodingStandard\TokenRunner\ValueObject;

final class LineLengthAndPosition
{
    /**
     * @var int
     */
    private $lineLength;
    /**
     * @var int
     */
    private $currentPosition;
    public function __construct(int $lineLength, int $currentPosition)
    {
        $this->lineLength = $lineLength;
        $this->currentPosition = $currentPosition;
    }
    public function getLineLength() : int
    {
        return $this->lineLength;
    }
    public function getCurrentPosition() : int
    {
        return $this->currentPosition;
    }
}
