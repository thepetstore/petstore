<?php
/**
 * Model to calculate shipping price as per item or per order (selected in the configuraion)
 */
namespace Infobeans\FlatRate5\Model\Carrier\Flatrate;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class ItemPriceCalculator
 */
class ItemPriceCalculator
{
    /**
     * @param RateRequest $request
     * @param int $basePrice
     * @param int $freeBoxes
     * @return float
     */
    public function getShippingPricePerItem(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $basePrice,
        $freeBoxes
    ) {
        return $request->getPackageQty() * $basePrice - $freeBoxes * $basePrice;
    }

    /**
     * @param RateRequest $request
     * @param int $basePrice
     * @param int $freeBoxes
     * @return float
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getShippingPricePerOrder(
        \Magento\Quote\Model\Quote\Address\RateRequest $request,
        $basePrice,
        $freeBoxes
    ) {
        return $basePrice;
    }
}