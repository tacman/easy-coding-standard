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
namespace PhpCsFixer;

use PhpCsFixer\Fixer\FixerInterface;
/**
 * @author Fabien Potencier <fabien@symfony.com>
 * @author Katsuhiro Ogawa <ko.fivestar@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
class Config implements \PhpCsFixer\ConfigInterface
{
    /**
     * @var string
     */
    private $cacheFile = '.php-cs-fixer.cache';
    /**
     * @var FixerInterface[]
     */
    private $customFixers = [];
    /**
     * @var null|iterable
     */
    private $finder;
    /**
     * @var string
     */
    private $format = 'txt';
    /**
     * @var bool
     */
    private $hideProgress = \false;
    /**
     * @var string
     */
    private $indent = '    ';
    /**
     * @var bool
     */
    private $isRiskyAllowed = \false;
    /**
     * @var string
     */
    private $lineEnding = "\n";
    /**
     * @var string
     */
    private $name;
    /**
     * @var null|string
     */
    private $phpExecutable;
    /**
     * @var mixed[]
     */
    private $rules = ['@PSR12' => \true];
    /**
     * @var bool
     */
    private $usingCache = \true;
    public function __construct(string $name = 'default')
    {
        $this->name = $name;
    }
    /**
     * {@inheritdoc}
     */
    public function getCacheFile() : ?string
    {
        return $this->cacheFile;
    }
    /**
     * {@inheritdoc}
     */
    public function getCustomFixers() : array
    {
        return $this->customFixers;
    }
    /**
     * @return Finder
     */
    public function getFinder() : iterable
    {
        if (null === $this->finder) {
            $this->finder = new \PhpCsFixer\Finder();
        }
        return $this->finder;
    }
    /**
     * {@inheritdoc}
     */
    public function getFormat() : string
    {
        return $this->format;
    }
    /**
     * {@inheritdoc}
     */
    public function getHideProgress() : bool
    {
        return $this->hideProgress;
    }
    /**
     * {@inheritdoc}
     */
    public function getIndent() : string
    {
        return $this->indent;
    }
    /**
     * {@inheritdoc}
     */
    public function getLineEnding() : string
    {
        return $this->lineEnding;
    }
    /**
     * {@inheritdoc}
     */
    public function getName() : string
    {
        return $this->name;
    }
    /**
     * {@inheritdoc}
     */
    public function getPhpExecutable() : ?string
    {
        return $this->phpExecutable;
    }
    /**
     * {@inheritdoc}
     */
    public function getRiskyAllowed() : bool
    {
        return $this->isRiskyAllowed;
    }
    /**
     * {@inheritdoc}
     */
    public function getRules() : array
    {
        return $this->rules;
    }
    /**
     * {@inheritdoc}
     */
    public function getUsingCache() : bool
    {
        return $this->usingCache;
    }
    /**
     * {@inheritdoc}
     */
    public function registerCustomFixers(iterable $fixers) : \PhpCsFixer\ConfigInterface
    {
        foreach ($fixers as $fixer) {
            $this->addCustomFixer($fixer);
        }
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setCacheFile(string $cacheFile) : \PhpCsFixer\ConfigInterface
    {
        $this->cacheFile = $cacheFile;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setFinder(iterable $finder) : \PhpCsFixer\ConfigInterface
    {
        $this->finder = $finder;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setFormat(string $format) : \PhpCsFixer\ConfigInterface
    {
        $this->format = $format;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setHideProgress(bool $hideProgress) : \PhpCsFixer\ConfigInterface
    {
        $this->hideProgress = $hideProgress;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setIndent(string $indent) : \PhpCsFixer\ConfigInterface
    {
        $this->indent = $indent;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setLineEnding(string $lineEnding) : \PhpCsFixer\ConfigInterface
    {
        $this->lineEnding = $lineEnding;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setPhpExecutable(?string $phpExecutable) : \PhpCsFixer\ConfigInterface
    {
        $this->phpExecutable = $phpExecutable;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setRiskyAllowed(bool $isRiskyAllowed) : \PhpCsFixer\ConfigInterface
    {
        $this->isRiskyAllowed = $isRiskyAllowed;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setRules(array $rules) : \PhpCsFixer\ConfigInterface
    {
        $this->rules = $rules;
        return $this;
    }
    /**
     * {@inheritdoc}
     */
    public function setUsingCache(bool $usingCache) : \PhpCsFixer\ConfigInterface
    {
        $this->usingCache = $usingCache;
        return $this;
    }
    private function addCustomFixer(FixerInterface $fixer) : void
    {
        $this->customFixers[] = $fixer;
    }
}
