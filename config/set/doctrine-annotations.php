<?php

declare (strict_types=1);
namespace ECSPrefix20220607;

use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationArrayAssignmentFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationIndentationFixer;
use PhpCsFixer\Fixer\DoctrineAnnotation\DoctrineAnnotationSpacesFixer;
use ECSPrefix20220607\Symplify\EasyCodingStandard\Config\ECSConfig;
return static function (ECSConfig $ecsConfig) : void {
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationIndentationFixer::class, ['indent_mixed_lines' => \true]);
    $ecsConfig->ruleWithConfiguration(DoctrineAnnotationSpacesFixer::class, ['after_array_assignments_equals' => \false, 'before_array_assignments_equals' => \false]);
    $ecsConfig->rule(DoctrineAnnotationArrayAssignmentFixer::class);
};
