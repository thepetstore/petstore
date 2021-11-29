<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Ui\DataProvider;

use Aheadworks\AdvancedReports\Model\Period as PeriodModel;
use Aheadworks\AdvancedReports\Model\Serializer;
use Magento\Framework\Api\AttributeValueFactory;
use Aheadworks\AdvancedReports\Model\Filter\FilterPool;

/**
 * Class Document
 *
 * @package Aheadworks\AdvancedReports\Ui\DataProvider
 */
class Document extends \Magento\Framework\View\Element\UiComponent\DataProvider\Document
{
    /**
     * @var PeriodModel
     */
    private $periodModel;

    /**
     * @var Serializer;
     */
    private $serializer;

    /**
     * @var FilterPool
     */
    private $filterPool;

    /**
     * @param AttributeValueFactory $attributeValueFactory
     * @param PeriodModel $periodModel
     * @param Serializer $serializer
     * @param FilterPool $filterPool
     */
    public function __construct(
        AttributeValueFactory $attributeValueFactory,
        PeriodModel $periodModel,
        Serializer $serializer,
        FilterPool $filterPool
    ) {
        parent::__construct($attributeValueFactory);
        $this->periodModel = $periodModel;
        $this->serializer = $serializer;
        $this->filterPool = $filterPool;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomAttribute($attributeCode)
    {
        if ($attributeCode == 'period') {
            $this->preparePeriodField($attributeCode);
        }
        if ($attributeCode == 'method') {
            $this->preparePaymentMethodField($attributeCode);
        }

        return parent::getCustomAttribute($attributeCode);
    }

    /**
     * Prepare period field
     *
     * @param string $attributeCode
     * @return void
     */
    private function preparePeriodField($attributeCode)
    {
        $data = $this->getData();
        $periodFilter = $this->filterPool->getFilter('period');
        $groupBy = $this->filterPool->getFilter('group_by')->getValue();
        $periodFrom = $periodFilter->getPeriodFrom();
        $periodTo = $periodFilter->getPeriodTo();

        $periodDates = $this->periodModel->periodDatesResolve($data, $groupBy, $periodFrom, $periodTo, false);
        $periodLabel = $this->periodModel->getPeriodAsString(
            $periodDates['start_date'],
            $periodDates['end_date'],
            $groupBy
        );
        $this->setCustomAttribute($attributeCode, $periodLabel);
    }

    /**
     * Prepare payment method field
     *
     * @param string $attributeCode
     * @return void
     */
    private function preparePaymentMethodField($attributeCode)
    {
        $item = $this->getData();
        $methodName = $item['method'];
        if (isset($item['additional_info']) && $item['additional_info']) {
            $additionalInfo = $this->serializer->unserialize($item['additional_info']);
            if (isset($additionalInfo['method_title'])) {
                $methodName = $additionalInfo['method_title'];
            }
        }
        $this->setCustomAttribute($attributeCode, $methodName . ' (' . $item['method'] . ')');
    }
}
