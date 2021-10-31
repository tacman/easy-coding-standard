<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace ECSPrefix20211031\Symfony\Component\HttpKernel\HttpCache;

use ECSPrefix20211031\Symfony\Component\HttpFoundation\IpUtils;
use ECSPrefix20211031\Symfony\Component\HttpFoundation\Request;
use ECSPrefix20211031\Symfony\Component\HttpFoundation\Response;
use ECSPrefix20211031\Symfony\Component\HttpKernel\HttpKernelInterface;
/**
 * @author Nicolas Grekas <p@tchwork.com>
 *
 * @internal
 */
class SubRequestHandler
{
    public static function handle(\ECSPrefix20211031\Symfony\Component\HttpKernel\HttpKernelInterface $kernel, \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request $request, int $type, bool $catch) : \ECSPrefix20211031\Symfony\Component\HttpFoundation\Response
    {
        // save global state related to trusted headers and proxies
        $trustedProxies = \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::getTrustedProxies();
        $trustedHeaderSet = \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::getTrustedHeaderSet();
        // remove untrusted values
        $remoteAddr = $request->server->get('REMOTE_ADDR');
        if (!\ECSPrefix20211031\Symfony\Component\HttpFoundation\IpUtils::checkIp($remoteAddr, $trustedProxies)) {
            $trustedHeaders = ['FORWARDED' => $trustedHeaderSet & \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED, 'X_FORWARDED_FOR' => $trustedHeaderSet & \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR, 'X_FORWARDED_HOST' => $trustedHeaderSet & \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_HOST, 'X_FORWARDED_PROTO' => $trustedHeaderSet & \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PROTO, 'X_FORWARDED_PORT' => $trustedHeaderSet & \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_PORT];
            foreach (\array_filter($trustedHeaders) as $name => $key) {
                $request->headers->remove($name);
                $request->server->remove('HTTP_' . $name);
            }
        }
        // compute trusted values, taking any trusted proxies into account
        $trustedIps = [];
        $trustedValues = [];
        foreach (\array_reverse($request->getClientIps()) as $ip) {
            $trustedIps[] = $ip;
            $trustedValues[] = \sprintf('for="%s"', $ip);
        }
        if ($ip !== $remoteAddr) {
            $trustedIps[] = $remoteAddr;
            $trustedValues[] = \sprintf('for="%s"', $remoteAddr);
        }
        // set trusted values, reusing as much as possible the global trusted settings
        if (\ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED & $trustedHeaderSet) {
            $trustedValues[0] .= \sprintf(';host="%s";proto=%s', $request->getHttpHost(), $request->getScheme());
            $request->headers->set('Forwarded', $v = \implode(', ', $trustedValues));
            $request->server->set('HTTP_FORWARDED', $v);
        }
        if (\ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR & $trustedHeaderSet) {
            $request->headers->set('X-Forwarded-For', $v = \implode(', ', $trustedIps));
            $request->server->set('HTTP_X_FORWARDED_FOR', $v);
        } elseif (!(\ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_FORWARDED & $trustedHeaderSet)) {
            \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::setTrustedProxies($trustedProxies, $trustedHeaderSet | \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::HEADER_X_FORWARDED_FOR);
            $request->headers->set('X-Forwarded-For', $v = \implode(', ', $trustedIps));
            $request->server->set('HTTP_X_FORWARDED_FOR', $v);
        }
        // fix the client IP address by setting it to 127.0.0.1,
        // which is the core responsibility of this method
        $request->server->set('REMOTE_ADDR', '127.0.0.1');
        // ensure 127.0.0.1 is set as trusted proxy
        if (!\ECSPrefix20211031\Symfony\Component\HttpFoundation\IpUtils::checkIp('127.0.0.1', $trustedProxies)) {
            \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::setTrustedProxies(\array_merge($trustedProxies, ['127.0.0.1']), \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::getTrustedHeaderSet());
        }
        try {
            return $kernel->handle($request, $type, $catch);
        } finally {
            // restore global state
            \ECSPrefix20211031\Symfony\Component\HttpFoundation\Request::setTrustedProxies($trustedProxies, $trustedHeaderSet);
        }
    }
}
