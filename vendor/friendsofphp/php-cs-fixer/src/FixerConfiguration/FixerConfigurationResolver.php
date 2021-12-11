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
namespace PhpCsFixer\FixerConfiguration;

use PhpCsFixer\Utils;
use ECSPrefix20211211\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;
use ECSPrefix20211211\Symfony\Component\OptionsResolver\OptionsResolver;
final class FixerConfigurationResolver implements \PhpCsFixer\FixerConfiguration\FixerConfigurationResolverInterface
{
    /**
     * @var FixerOptionInterface[]
     */
    private $options = [];
    /**
     * @var string[]
     */
    private $registeredNames = [];
    /**
     * @param iterable<FixerOptionInterface> $options
     */
    public function __construct(iterable $options)
    {
        foreach ($options as $option) {
            $this->addOption($option);
        }
        if (empty($this->registeredNames)) {
            throw new \LogicException('Options cannot be empty.');
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getOptions() : array
    {
        return $this->options;
    }
    /**
     * {@inheritdoc}
     */
    public function resolve(array $configuration) : array
    {
        $resolver = new \ECSPrefix20211211\Symfony\Component\OptionsResolver\OptionsResolver();
        foreach ($this->options as $option) {
            $name = $option->getName();
            if ($option instanceof \PhpCsFixer\FixerConfiguration\AliasedFixerOption) {
                $alias = $option->getAlias();
                if (\array_key_exists($alias, $configuration)) {
                    if (\array_key_exists($name, $configuration)) {
                        throw new \ECSPrefix20211211\Symfony\Component\OptionsResolver\Exception\InvalidOptionsException(\sprintf('Aliased option "%s"/"%s" is passed multiple times.', $name, $alias));
                    }
                    \PhpCsFixer\Utils::triggerDeprecation(new \RuntimeException(\sprintf('Option "%s" is deprecated, use "%s" instead.', $alias, $name)));
                    $configuration[$name] = $configuration[$alias];
                    unset($configuration[$alias]);
                }
            }
            if ($option->hasDefault()) {
                $resolver->setDefault($name, $option->getDefault());
            } else {
                $resolver->setRequired($name);
            }
            $allowedValues = $option->getAllowedValues();
            if (null !== $allowedValues) {
                foreach ($allowedValues as &$allowedValue) {
                    if (\is_object($allowedValue) && \is_callable($allowedValue)) {
                        $allowedValue = static function ($values) use($allowedValue) {
                            return $allowedValue($values);
                        };
                    }
                }
                $resolver->setAllowedValues($name, $allowedValues);
            }
            $allowedTypes = $option->getAllowedTypes();
            if (null !== $allowedTypes) {
                $resolver->setAllowedTypes($name, $allowedTypes);
            }
            $normalizer = $option->getNormalizer();
            if (null !== $normalizer) {
                $resolver->setNormalizer($name, $normalizer);
            }
        }
        return $resolver->resolve($configuration);
    }
    /**
     * @throws \LogicException when the option is already defined
     */
    private function addOption(\PhpCsFixer\FixerConfiguration\FixerOptionInterface $option) : void
    {
        $name = $option->getName();
        if (\in_array($name, $this->registeredNames, \true)) {
            throw new \LogicException(\sprintf('The "%s" option is defined multiple times.', $name));
        }
        $this->options[] = $option;
        $this->registeredNames[] = $name;
    }
}
