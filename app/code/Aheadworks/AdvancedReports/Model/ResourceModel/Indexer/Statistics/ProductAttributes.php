<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics;

/**
 * Class ProductAttributes
 *
 * @package Aheadworks\AdvancedReports\Model\ResourceModel\Indexer\Statistics
 */
class ProductAttributes extends AbstractResource
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('aw_arep_product_attributes', 'id');
    }

    /**
     * {@inheritdoc}
     */
    protected function process()
    {
        $period = $this->getPeriod('order.created_at');
        $columns = [
            'period' => $period,
            'store_id' => 'order.store_id',
            'order_status' => 'order.status',
            'customer_group_id' => 'order.customer_group_id',
            'product_id' => 'main_table.product_id',
            'order_items_count' => 'SUM(COALESCE(main_table.qty_ordered, 0))',
            'subtotal' => 'SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))',
            'tax' => 'SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))',
            'total' => '(SUM(COALESCE(configurable.base_row_total, main_table.base_row_total, 0.0))
                + SUM(COALESCE(configurable.base_tax_amount, main_table.base_tax_amount, 0.0))
                - SUM(COALESCE(configurable.base_discount_amount, main_table.base_discount_amount, 0.0)))',
            'invoiced' => '(SUM(COALESCE(configurable.base_row_invoiced, main_table.base_row_invoiced, 0.0))
                + SUM(COALESCE(configurable.base_tax_invoiced, main_table.base_tax_invoiced, 0.0))
                - SUM(COALESCE(configurable.base_discount_invoiced, main_table.base_discount_invoiced, 0.0)))',
            'refunded' => '(SUM(COALESCE(configurable.base_amount_refunded, main_table.base_amount_refunded, 0.0)) '
                . ' + SUM(COALESCE(configurable.base_tax_refunded, main_table.base_tax_refunded, 0.0)) '
                . ' - SUM(COALESCE(configurable.base_discount_refunded, main_table.base_discount_refunded, 0.0)))',
            'to_global_rate' => 'order.base_to_global_rate'
        ];
        $orderItemTable = $this->getTable('sales_order_item');

        $select = $this->getConnection()->select()
            ->from(['main_table' => $orderItemTable], [])
            ->columns($columns)
            ->joinLeft(
                ['order' => $this->getTable('sales_order')],
                'order.entity_id = main_table.order_id',
                []
            )->joinLeft(
                ['bundle_item_price' => $this->getBundleItemsPrice()],
                '(order.entity_id = bundle_item_price.order_id AND '
                . 'main_table.item_id = bundle_item_price.parent_item_id)',
                []
            )->joinLeft(
                ['configurable' => $orderItemTable],
                '(order.entity_id = configurable.order_id AND main_table.parent_item_id = configurable.item_id '
                . 'AND configurable.product_type IN ("configurable"))',
                []
            )
            ->joinLeft(
                ['parent' => $orderItemTable],
                '(order.entity_id = parent.order_id AND main_table.parent_item_id = parent.item_id)',
                []
            )
            ->where('(main_table.product_type <> "bundle" OR bundle_item_price.price = 0)')
            ->where('(main_table.product_type <> "configurable")')
            ->group(
                [
                    'order.status',
                    $period,
                    'order.store_id',
                    'main_table.product_id',
                    'order.base_to_global_rate',
                    'order.customer_group_id'
                ]
            );
        $select = $this->addFilterByCreatedAt($select, 'main_table');

        $this->safeInsertFromSelect($select, $this->getIdxTable(), array_keys($columns));
    }
}
