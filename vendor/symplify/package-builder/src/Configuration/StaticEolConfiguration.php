<?php

declare (strict_types=1);
namespace ECSPrefix20211214\Symplify\PackageBuilder\Configuration;

/**
 * @api
 */
final class StaticEolConfiguration
{
    public static function getEolChar() : string
    {
        return "\n";
    }
}
