<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_LazyLoad
 */


namespace Amasty\LazyLoad\Plugin\Framework\App\PageCache;

use Amasty\PageSpeedTools\Model\DeviceDetect;
use Amasty\LazyLoad\Model\ConfigProvider;

/**
 * Plugin change cache key to show correct pages for different devices
 */
class CacheIdentifierPlugin
{
    /**
     * @var DeviceDetect
     */
    private $deviceDetect;

    /**
     * @var ConfigProvider
     */
    private $configProvider;

    public function __construct(
        DeviceDetect $deviceDetect,
        ConfigProvider $configProvider
    ) {
        $this->deviceDetect = $deviceDetect;
        $this->configProvider = $configProvider;
    }

    /**
     * @param \Magento\Framework\App\PageCache\Identifier $identifier
     * @param string $result
     * @return string
     */
    public function afterGetValue(\Magento\Framework\App\PageCache\Identifier $identifier, $result)
    {
        if (!$this->configProvider->isEnabled() || !$this->configProvider->isReplaceImagesUsingUserAgent()) {
            return $result;
        }

        return $result . 'amasty_' . $this->deviceDetect->getDeviceType() . (int)$this->deviceDetect->isUseWebP();
    }
}
