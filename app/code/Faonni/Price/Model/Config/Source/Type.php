<?php
/**
 * Copyright © Karliuka Vitalii(karliuka.vitalii@gmail.com)
 * See COPYING.txt for license details.
 */
namespace Faonni\Price\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;
use Faonni\Price\Model\Math;

/**
 * Round Type Source Option
 */
class Type implements ArrayInterface
{
    /**
     * Return array of options as value-label pairs
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => Math::TYPE_CEIL,  'label' => __('Round fractions up')],
            ['value' => Math::TYPE_FLOOR, 'label' => __('Round fractions down')],
            ['value' => Math::TYPE_SWEDISH_CEIL,  'label' => __('Swedish Round up')],
            ['value' => Math::TYPE_SWEDISH_ROUND, 'label' => __('Swedish Round')],
            ['value' => Math::TYPE_SWEDISH_FLOOR, 'label' => __('Swedish Round down')],
            ['value' => Math::TYPE_EXCEL_CEIL,  'label' => __('Excel Round up')],
            ['value' => Math::TYPE_EXCEL_ROUND, 'label' => __('Excel Round')],
            ['value' => Math::TYPE_EXCEL_FLOOR, 'label' => __('Excel Round down')]
        ];
    }
}
