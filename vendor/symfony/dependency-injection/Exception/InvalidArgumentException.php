<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211122\Symfony\Component\DependencyInjection\Exception;

/**
 * Base InvalidArgumentException for Dependency Injection component.
 *
 * @author Bulat Shakirzyanov <bulat@theopenskyproject.com>
 */
class InvalidArgumentException extends \InvalidArgumentException implements \ECSPrefix20211122\Symfony\Component\DependencyInjection\Exception\ExceptionInterface
{
}
