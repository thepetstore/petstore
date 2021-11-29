<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_ImageOptimizer
 */

declare(strict_types=1);

namespace Amasty\ImageOptimizer\Model\Image;

use Amasty\ImageOptimizer\Api\ImageQueueServiceInterface;
use Amasty\ImageOptimizer\Model\ImageProcessor;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Filesystem\Io\File;

class GenerateQueue
{
    /**
     * @var \Amasty\ImageOptimizer\Model\Queue\ImageQueueService
     */
    private $imageQueueService;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    private $mediaDirectory;

    /**
     * @var File
     */
    private $file;

    /**
     * @var ImageProcessor
     */
    private $imageProcessor;

    public function __construct(
        ImageQueueServiceInterface $imageQueueService,
        Filesystem $filesystem,
        File $file,
        ImageProcessor $imageProcessor
    ) {
        $this->imageQueueService = $imageQueueService;
        $this->mediaDirectory = $filesystem->getDirectoryRead(DirectoryList::MEDIA);
        $this->file = $file;
        $this->imageProcessor = $imageProcessor;
    }

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface[] $imageSettings
     *
     * @return int
     */
    public function generateQueue(array $imageSettings): int
    {
        $this->imageQueueService->clearQueue();
        $this->processFiles($imageSettings);

        return $this->imageQueueService->getQueueSize();
    }

    /**
     * @param \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface[] $imageSettings
     *
     * @return void
     */
    public function processFiles(array $imageSettings): void
    {
        $folders = [];
        /** @var \Amasty\ImageOptimizer\Api\Data\ImageSettingInterface $item */
        foreach ($imageSettings as $item) {
            foreach ($item->getFolders() as $folder) {
                $folders[$folder] = $item;
            }
        }

        foreach ($folders as $imageDirectory => $imageSetting) {
            $files = $this->mediaDirectory->readRecursively($imageDirectory);
            foreach ($files as $file) {
                $pathInfo = $this->file->getPathInfo($file);
                if ($pathInfo['dirname'] !== $imageDirectory && isset($imageFolders[$pathInfo['dirname']])) {
                    continue;
                }
                if ($queue = $this->imageProcessor->prepareQueue($file, $imageSetting)) {
                    $this->imageQueueService->addToQueue($queue);
                }
            }
        }
    }
}
