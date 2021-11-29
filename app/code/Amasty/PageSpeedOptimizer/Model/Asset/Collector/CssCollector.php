<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */


declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Model\Asset\Collector;

class CssCollector extends AbstractAssetCollector
{
    const REGEX = '<link.*href=["|\'](?<asset_url>.*\.css).*>';

    public function getAssetContentType(): string
    {
        return 'style';
    }
}
