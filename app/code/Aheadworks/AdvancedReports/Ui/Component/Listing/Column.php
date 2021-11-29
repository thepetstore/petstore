<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Ui\Component\Listing;

/**
 * Class Column
 *
 * @package Aheadworks\AdvancedReports\Ui\Component\Listing
 */
class Column extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var array
     */
    private $colors = [
        '#556dd3' => ['orders_count', 'total_carts'],
        '#00ae97' => ['order_items_count', 'qty_invoiced', 'views_count', 'completed_carts'],
        '#b34852' => ['subtotal', 'conversion_rate', 'abandoned_carts'],
        '#036485' => ['tax', 'customers_count'],
        '#ff748e' => ['shipping'],
        '#8450d5' => ['discount', 'other_discount'],
        '#77523f' => ['total', 'total_sales_percent', 'abandoned_carts_total'],
        '#00a2ff' => ['invoiced', 'total_sales'],
        '#a5927d' => ['refunded', 'abandonment_rate'],
        '#ff7f00' => ['total_profit']
    ];

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();
        $config = $this->getData('config');

        if (!isset($config['color'])) {
            $fieldName = $this->getData('name');
            if ($color = $this->getColorByFieldName($fieldName)) {
                $config['color'] = $color;
            }
        }

        $this->setData('config', $config);
    }

    /**
     * Retrieve color by field name
     *
     * @param $fieldName
     * @return bool|string
     */
    private function getColorByFieldName($fieldName)
    {
        foreach ($this->colors as $color => $fields) {
            if (in_array($fieldName, $fields)) {
                return $color;
            }
        }

        return false;
    }
}
