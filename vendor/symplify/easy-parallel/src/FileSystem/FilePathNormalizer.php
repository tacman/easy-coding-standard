<?php

declare (strict_types=1);
namespace ECSPrefix20211207\Symplify\EasyParallel\FileSystem;

use ECSPrefix20211207\Symplify\SmartFileSystem\SmartFileInfo;
final class FilePathNormalizer
{
    /**
     * @param SmartFileInfo[] $fileInfos
     * @return string[]
     */
    public function resolveFilePathsFromFileInfos(array $fileInfos) : array
    {
        $filePaths = [];
        foreach ($fileInfos as $fileInfo) {
            $filePaths[] = $fileInfo->getRelativeFilePathFromCwd();
        }
        return $filePaths;
    }
}