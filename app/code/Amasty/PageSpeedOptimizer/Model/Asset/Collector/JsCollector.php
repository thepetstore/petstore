<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Asset\Collector;

class JsCollector extends AbstractAssetCollector
{
    const REGEX = '<script[^>]*?src=["|\'](?<asset_url>.*\.js)[^>]*?><\/script>';

    public function getAssetContentType(): string
    {
        return 'script';
    }
}
