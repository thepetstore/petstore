<?php
/**
 * Option array to apply shipping per order or per item
 */
namespace Infobeans\FlatRate5\Model\Config\Source;

/**
 * Class Flatrate
 */
class Flatrate implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => '', 'label' => __('None')],
            ['value' => 'O', 'label' => __('Per Order')],
            ['value' => 'I', 'label' => __('Per Item')]
        ];
    }
}
