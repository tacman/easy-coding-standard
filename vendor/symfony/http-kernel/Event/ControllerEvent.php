<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20210626\Symfony\Component\HttpKernel\Event;

use ECSPrefix20210626\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20210626\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * Allows filtering of a controller callable.
 *
 * You can call getController() to retrieve the current controller. With
 * setController() you can set a new controller that is used in the processing
 * of the request.
 *
 * Controllers should be callables.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
final class ControllerEvent extends \ECSPrefix20210626\Symfony\Component\HttpKernel\Event\KernelEvent
{
    private $controller;
    /**
     * @param int|null $requestType
     */
    public function __construct(\ECSPrefix20210626\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, callable $controller, \ECSPrefix20210626\Symfony\Component\HttpFoundation\Request $request, $requestType)
    {
        parent::__construct($kernel, $request, $requestType);
        $this->setController($controller);
    }
    public function getController() : callable
    {
        return $this->controller;
    }
    /**
     * @return void
     */
    public function setController(callable $controller)
    {
        $this->controller = $controller;
    }
}
