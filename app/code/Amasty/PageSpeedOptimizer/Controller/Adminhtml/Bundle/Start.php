<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_PageSpeedOptimizer
 */

declare(strict_types=1);

namespace Amasty\PageSpeedOptimizer\Controller\Adminhtml\Bundle;

use Amasty\PageSpeedOptimizer\Model\Bundle\ResourceModel\Bundle;
use Amasty\PageSpeedOptimizer\Model\ConfigProvider;
use Amasty\PageSpeedOptimizer\Model\OptionSource\BundlingType;
use Magento\Backend\App\Action;
use Magento\Framework\App\Area;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Math\Random;
use Magento\Framework\View\DesignInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\ResourceModel\Theme\CollectionFactory as ThemeCollectionFactory;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollectionFactory;

class Start extends Action
{
    /**
     * @var ConfigProvider
     */
    private $configProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var TypeListInterface
     */
    private $cache;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var Bundle
     */
    private $bundleResource;

    /**
     * @var string
     */
    private $bundleHash;

    /**
     * @var string
     */
    private $rand;

    /**
     * @var UrlRewriteCollectionFactory
     */
    private $urlRewriteCollectionFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ThemeCollectionFactory
     */
    private $themeCollectionFactory;

    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var Random
     */
    private $random;

    public function __construct(
        ConfigProvider $configProvider,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        TypeListInterface $cache,
        WriterInterface $configWriter,
        Bundle $bundleResource,
        UrlRewriteCollectionFactory $urlRewriteCollectionFactory,
        ThemeCollectionFactory $themeCollectionFactory,
        Random $random,
        DesignInterface $design,
        Action\Context $context
    ) {
        parent::__construct($context);
        $this->configProvider = $configProvider;
        $this->storeManager = $storeManager;
        $this->cache = $cache;
        $this->configWriter = $configWriter;
        $this->bundleResource = $bundleResource;
        $this->urlRewriteCollectionFactory = $urlRewriteCollectionFactory;
        $this->scopeConfig = $scopeConfig;
        $this->themeCollectionFactory = $themeCollectionFactory;
        $this->design = $design;
        $this->random = $random;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $this->bundleResource->clear();
        $this->bundleHash = $this->random->getUniqueHash();
        $this->configWriter->save(
            'amoptimizer/' . ConfigProvider::IS_CLOUD,
            (bool)$this->getRequest()->getParam('isCloud', false)
        );
        $this->configWriter->save('amoptimizer/' . ConfigProvider::BUNDLE_HASH, $this->bundleHash);
        $this->configWriter->save('amoptimizer/' . ConfigProvider::BUNDLING_TYPE, BundlingType::SUPER_BUNDLING);
        $this->cache->cleanType(\Magento\Framework\App\Cache\Type\Config::TYPE_IDENTIFIER);
        $result = [];
        $this->rand = $this->random->getUniqueHash();

        /** @var \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection */
        $themesData = $this->themeCollectionFactory->create()
            ->addAreaFilter('frontend')
            ->getData();
        $themes = [0 => __('Default')];
        $themePathIdMap = [];
        foreach ($themesData as $theme) {
            $themes[$theme['theme_id']] = $theme['theme_title'];
            $themePathIdMap[$theme['theme_path']] = $theme['theme_id'];
        }

        foreach ($this->storeManager->getStores() as $store) {
            $themeId = $this->design->getConfigurationDesignTheme(
                Area::AREA_FRONTEND,
                ['store' => $store->getId()]
            );
            //theme code workaround
            if (!is_numeric($themeId)) {
                if (isset($themePathIdMap[$themeId])) {
                    $themeId = $themePathIdMap[$themeId];
                } else {
                    $themeId = 0;
                }
            }
            $locale = $this->scopeConfig->getValue(
                \Magento\Directory\Helper\Data::XML_PATH_DEFAULT_LOCALE,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $store->getId()
            );
            $storeData = [
                'title' => $store->getName(),
                'store_id' => $store->getId(),
                'urls' => [
                    $this->getBundleUrl((int)$store->getId(), '')
                ],
            ];

            if ($url = $this->getSimpleProductUrl((int)$store->getId())) {
                $storeData['urls'][] = $url;
            }

            if ($url = $this->getConfigurableProductUrl((int)$store->getId())) {
                $storeData['urls'][] = $url;
            }

            if ($urls = $this->getCategoryUrls((int)$store->getId())) {
                array_push($storeData['urls'], ...$urls);
            }

            $result[$themes[$themeId]][$locale][] = $storeData;
        }

        $forceProceed = true;
        foreach ($result as $locales) {
            foreach ($locales as $localeStores) {
                if (count($localeStores) > 1) {
                    $forceProceed = false;
                    break 2;
                }
            }
        }

        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData([
            'links' => $result,
            'add_param' => $this->getAddUrlParams(),
            'force_proceed' => $forceProceed
        ]);
    }

    public function getRewriteCollection(int $storeId, string $entityType): UrlRewriteCollection
    {
        return $this->urlRewriteCollectionFactory->create()
            ->addStoreFilter([$storeId], false)
            ->addFieldToFilter('entity_type', $entityType);
    }

    public function getProductUrlByType(int $storeId, string $productType): string
    {
        $collection = $this->getRewriteCollection($storeId, 'product');
        $collection->join(
            'catalog_product_entity',
            'main_table.entity_id = catalog_product_entity.entity_id'
        )->addFieldToFilter('catalog_product_entity.type_id', $productType);
        $collection->getSelect()->limit(1);

        if ($item = $collection->getFirstItem()) {
            return $this->getBundleUrl($storeId, $item->getRequestPath());
        }

        return '';
    }

    public function getSimpleProductUrl(int $storeId): string
    {
        return $this->getProductUrlByType($storeId, 'simple');
    }

    public function getConfigurableProductUrl(int $storeId): string
    {
        return $this->getProductUrlByType($storeId, 'configurable');
    }

    public function getCategoryUrls(int $storeId): array
    {
        $result = [];
        $collection = $this->getRewriteCollection($storeId, 'category')->setPageSize(2);

        foreach ($collection->getItems() as $item) {
            $result[] = $this->getBundleUrl($storeId, $item->getRequestPath());
        }

        if (!empty($result)) {
            return $result;
        }

        return [];
    }

    public function getBundleUrl(int $storeId, string $url): string
    {
        return $this->storeManager->getStore($storeId)->getBaseUrl()
            . $url . $this->getAddUrlParams()
            . '&___store=' . $this->storeManager->getStore($storeId)->getCode();
    }

    public function getAddUrlParams(): string
    {
        return '?' . http_build_query([
                'amoptimizer_bundle_check' => $this->bundleHash,
                'bu' => $this->_url->getBaseUrl(),
                'amoptimizer_not_move' => 1,
                'rand' => $this->rand
            ]);
    }
}
