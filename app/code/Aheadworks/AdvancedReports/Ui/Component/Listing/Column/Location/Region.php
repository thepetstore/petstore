<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Ui\Component\Listing\Column\Location;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Aheadworks\AdvancedReports\Model\Url as UrlModel;
use Aheadworks\AdvancedReports\Model\Source\Country as CountrySource;

/**
 * Class Region
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing\Column\Location
 */
class Region extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var UrlModel
     */
    private $urlModel;

    /**
     * @var CountrySource
     */
    private $countrySource;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlModel $urlModel
     * @param CountrySource $countrySource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlModel $urlModel,
        CountrySource $countrySource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlModel = $urlModel;
        $this->countrySource = $countrySource;
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item['row_label'] = $item['region'];
                $params = [
                    'country_id' => $item['country_id'],
                    'country_name' => base64_encode($this->countrySource->getOptionByValue($item['country_id'])),
                    'region' => base64_encode($item['row_label'])
                ];
                $item['row_url'] = $this->urlModel->getUrl(
                    'location_region',
                    'location_city',
                    $dataSource['data']['periodFromFilter'],
                    $dataSource['data']['periodToFilter'],
                    $params
                );
            }
        }
        return $dataSource;
    }
}
