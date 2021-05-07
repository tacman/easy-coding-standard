<?php

// scoper-autoload.php @generated by PhpScoper

$loader = require_once __DIR__.'/autoload.php';

// Aliases for the whitelisted classes. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#class-whitelisting
if (!class_exists('AutoloadIncluder', false) && !interface_exists('AutoloadIncluder', false) && !trait_exists('AutoloadIncluder', false)) {
    spl_autoload_call('ECSPrefix20210507\AutoloadIncluder');
}
if (!class_exists('Composer\InstalledVersions', false) && !interface_exists('Composer\InstalledVersions', false) && !trait_exists('Composer\InstalledVersions', false)) {
    spl_autoload_call('ECSPrefix20210507\Composer\InstalledVersions');
}
if (!class_exists('ComposerAutoloaderInitff85c343b7a82bcd14c712e1cd4695e9', false) && !interface_exists('ComposerAutoloaderInitff85c343b7a82bcd14c712e1cd4695e9', false) && !trait_exists('ComposerAutoloaderInitff85c343b7a82bcd14c712e1cd4695e9', false)) {
    spl_autoload_call('ECSPrefix20210507\ComposerAutoloaderInitff85c343b7a82bcd14c712e1cd4695e9');
}
if (!class_exists('Symfony\Component\DependencyInjection\Extension\ExtensionInterface', false) && !interface_exists('Symfony\Component\DependencyInjection\Extension\ExtensionInterface', false) && !trait_exists('Symfony\Component\DependencyInjection\Extension\ExtensionInterface', false)) {
    spl_autoload_call('ECSPrefix20210507\Symfony\Component\DependencyInjection\Extension\ExtensionInterface');
}
if (!class_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false) && !interface_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false) && !trait_exists('Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator', false)) {
    spl_autoload_call('ECSPrefix20210507\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator');
}

// Functions whitelisting. For more information see:
// https://github.com/humbug/php-scoper/blob/master/README.md#functions-whitelisting
if (!function_exists('composerRequireff85c343b7a82bcd14c712e1cd4695e9')) {
    function composerRequireff85c343b7a82bcd14c712e1cd4695e9() {
        return \ECSPrefix20210507\composerRequireff85c343b7a82bcd14c712e1cd4695e9(...func_get_args());
    }
}
if (!function_exists('sample')) {
    function sample() {
        return \ECSPrefix20210507\sample(...func_get_args());
    }
}
if (!function_exists('foo')) {
    function foo() {
        return \ECSPrefix20210507\foo(...func_get_args());
    }
}
if (!function_exists('bar')) {
    function bar() {
        return \ECSPrefix20210507\bar(...func_get_args());
    }
}
if (!function_exists('baz')) {
    function baz() {
        return \ECSPrefix20210507\baz(...func_get_args());
    }
}
if (!function_exists('xyz')) {
    function xyz() {
        return \ECSPrefix20210507\xyz(...func_get_args());
    }
}
if (!function_exists('printPHPCodeSnifferTestOutput')) {
    function printPHPCodeSnifferTestOutput() {
        return \ECSPrefix20210507\printPHPCodeSnifferTestOutput(...func_get_args());
    }
}
if (!function_exists('setproctitle')) {
    function setproctitle() {
        return \ECSPrefix20210507\setproctitle(...func_get_args());
    }
}
if (!function_exists('includeIfExists')) {
    function includeIfExists() {
        return \ECSPrefix20210507\includeIfExists(...func_get_args());
    }
}
if (!function_exists('dump')) {
    function dump() {
        return \ECSPrefix20210507\dump(...func_get_args());
    }
}
if (!function_exists('dd')) {
    function dd() {
        return \ECSPrefix20210507\dd(...func_get_args());
    }
}

return $loader;
