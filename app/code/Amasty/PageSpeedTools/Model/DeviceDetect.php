<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedTools
 */

declare(strict_types=1);

namespace Amasty\PageSpeedTools\Model;

use Amasty\PageSpeedTools\Lib\MobileDetect;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\PageCache\Model\Config;

class DeviceDetect extends MobileDetect
{
    const DESKTOP = 'desktop';
    const TABLET = 'tablet';
    const MOBILE = 'mobile';

    /**
     * @var string
     */
    private $webPBrowsersString = '/(Edge|Firefox|Chrome|Opera)/i';

    /**
     * @var string
     */
    private $deviceType;

    /**
     * @var bool
     */
    private $isWebpSupport;

    /**
     * @var CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        CookieManagerInterface $cookieManager,
        array $headers = null,
        $userAgent = null
    ) {
        parent::__construct($headers, $userAgent);
        $this->cookieManager = $cookieManager;
        $this->scopeConfig = $scopeConfig;
    }

    public function getDeviceParams(): array
    {
        if ($this->deviceType === null && $this->isWebpSupport === null) {
            if ($this->scopeConfig->getValue(Config::XML_PAGECACHE_TYPE) == Config::VARNISH
                && !$this->cookieManager->getCookie(Http::COOKIE_VARY_STRING)
            ) {
                $this->deviceType = \Amasty\PageSpeedTools\Model\DeviceDetect::DESKTOP;
                $this->isWebpSupport = false;
            } else {
                $this->deviceType = $this->detectDevice();
                $this->isWebpSupport = $this->detectIsUseWebp();
            }
        }

        return [$this->deviceType, $this->isWebpSupport];
    }

    public function getDeviceType(): string
    {
        [$deviceType] = $this->getDeviceParams();

        return $deviceType;
    }

    public function isUseWebP(): bool
    {
        [, $isWebpSupport] = $this->getDeviceParams();

        return $isWebpSupport;
    }

    protected function detectDevice(): string
    {
        if ($this->isTablet()) {
            return self::TABLET;
        }
        if ($this->isMobile()) {
            return self::MOBILE;
        }

        return self::DESKTOP;
    }

    protected function detectIsUseWebp(): bool
    {
        if (preg_match($this->webPBrowsersString, $this->getUserAgent())) {
            return true;
        }

        return false;
    }
}
