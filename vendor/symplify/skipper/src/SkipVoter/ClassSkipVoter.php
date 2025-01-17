<?php

declare (strict_types=1);
namespace ECSPrefix202206\Symplify\Skipper\SkipVoter;

use ECSPrefix202206\Symplify\PackageBuilder\Parameter\ParameterProvider;
use ECSPrefix202206\Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker;
use ECSPrefix202206\Symplify\Skipper\Contract\SkipVoterInterface;
use ECSPrefix202206\Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver;
use ECSPrefix202206\Symplify\Skipper\Skipper\OnlySkipper;
use ECSPrefix202206\Symplify\Skipper\Skipper\SkipSkipper;
use ECSPrefix202206\Symplify\Skipper\ValueObject\Option;
use ECSPrefix202206\Symplify\SmartFileSystem\SmartFileInfo;
final class ClassSkipVoter implements SkipVoterInterface
{
    /**
     * @var \Symplify\PackageBuilder\Reflection\ClassLikeExistenceChecker
     */
    private $classLikeExistenceChecker;
    /**
     * @var \Symplify\PackageBuilder\Parameter\ParameterProvider
     */
    private $parameterProvider;
    /**
     * @var \Symplify\Skipper\Skipper\SkipSkipper
     */
    private $skipSkipper;
    /**
     * @var \Symplify\Skipper\Skipper\OnlySkipper
     */
    private $onlySkipper;
    /**
     * @var \Symplify\Skipper\SkipCriteriaResolver\SkippedClassResolver
     */
    private $skippedClassResolver;
    public function __construct(ClassLikeExistenceChecker $classLikeExistenceChecker, ParameterProvider $parameterProvider, SkipSkipper $skipSkipper, OnlySkipper $onlySkipper, SkippedClassResolver $skippedClassResolver)
    {
        $this->classLikeExistenceChecker = $classLikeExistenceChecker;
        $this->parameterProvider = $parameterProvider;
        $this->skipSkipper = $skipSkipper;
        $this->onlySkipper = $onlySkipper;
        $this->skippedClassResolver = $skippedClassResolver;
    }
    /**
     * @param string|object $element
     */
    public function match($element) : bool
    {
        if (\is_object($element)) {
            return \true;
        }
        return $this->classLikeExistenceChecker->doesClassLikeExist($element);
    }
    /**
     * @param string|object $element
     */
    public function shouldSkip($element, SmartFileInfo $smartFileInfo) : bool
    {
        $only = $this->parameterProvider->provideArrayParameter(Option::ONLY);
        $doesMatchOnly = $this->onlySkipper->doesMatchOnly($element, $smartFileInfo, $only);
        if (\is_bool($doesMatchOnly)) {
            return $doesMatchOnly;
        }
        $skippedClasses = $this->skippedClassResolver->resolve();
        return $this->skipSkipper->doesMatchSkip($element, $smartFileInfo, $skippedClasses);
    }
}
