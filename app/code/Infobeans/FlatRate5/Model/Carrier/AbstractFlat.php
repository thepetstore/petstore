<?php
/**
 * Flatrate shipping model
 */
namespace Infobeans\FlatRate5\Model\Carrier;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Config;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\ScopeInterface;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Infobeans\FlatRate5\Model\Carrier\Flatrate\ItemPriceCalculator;
use Psr\Log\LoggerInterface;

/*
 * Class AbstractFlat
 */
abstract class AbstractFlat extends AbstractCarrier implements CarrierInterface {

    /**
     * Carrier's code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * Whether this carrier has fixed rates calculation
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * @var MethodFactory
     */
    protected $_rateMethodFactory;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param ItemPriceCalculator $itemPriceCalculator
     * @param array $data
     */
    public function __construct(
            ScopeConfigInterface $scopeConfig,
            ErrorFactory $rateErrorFactory,
            LoggerInterface $logger,
            ResultFactory $rateResultFactory,
            MethodFactory $rateMethodFactory,
            ItemPriceCalculator $itemPriceCalculator,
            array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->itemPriceCalculator = $itemPriceCalculator;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Generates list of allowed carrier`s shipping methods
     * Displays on cart price rules page
     *
     * @return array
     * @api
     */
    public function getAllowedMethods() {
        return [$this->getCarrierCode() => __($this->getConfigData('name'))];
    }

    /**
     * @param RateRequest $request
     * @return Result|bool
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function collectRates(RateRequest $request) {
        /**
         * Make sure that Shipping method is enabled
         */
        if (!$this->isActive()) {
            return false;
        }

        $freeBoxes = $this->getFreeBoxesCount($request);
        $this->setFreeBoxes($freeBoxes);

        /** @var Result $result */
        $result = $this->_rateResultFactory->create();

        $shippingPrice = $this->getShippingPrice($request, $freeBoxes);

        if ($shippingPrice !== false) {
            $method = $this->createResultMethod($shippingPrice);
            $result->append($method);
        }

        return $result;
    }

    /**
     * @param RateRequest $request
     * @return int
     */
    protected function getFreeBoxesCount(RateRequest $request) {
        $freeBoxes = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    $freeBoxes += $this->getFreeBoxesCountFromChildren($item);
                } elseif ($item->getFreeShipping()) {
                    $freeBoxes += $item->getQty();
                }
            }
        }
        return $freeBoxes;
    }

    /**
     * @param RateRequest $request
     * @param int $freeBoxes
     * @return bool|float
     */
    protected function getShippingPrice(RateRequest $request, $freeBoxes) {
        $shippingPrice = false;

        $configPrice = $this->getConfigData('price');
        if ($this->getConfigData('type') === 'O') {
            // per order
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerOrder($request, $configPrice, $freeBoxes);
        } elseif ($this->getConfigData('type') === 'I') {
            // per item
            $shippingPrice = $this->itemPriceCalculator->getShippingPricePerItem($request, $configPrice, $freeBoxes);
        }

        $shippingPrice = $this->getFinalPriceWithHandlingFee($shippingPrice);

        if ($shippingPrice !== false && (
                $request->getFreeShipping() === true || $request->getPackageQty() == $freeBoxes
                )
        ) {
            $shippingPrice = '0.00';
        }

        if ((($this->getConfigData('free_shipping_minimum_subtotal') != '') 
                && ($request->getBaseSubtotalInclTax() >= $this->getConfigData('free_shipping_minimum_subtotal'))) 
                && (($this->getConfigData('free_shipping_maximum_subtotal') != '') 
                && ($request->getBaseSubtotalInclTax() <= $this->getConfigData('free_shipping_maximum_subtotal')))) {
            $shippingPrice = '0.00';
        } else if ((($this->getConfigData('free_shipping_minimum_subtotal') != '') 
                && ($request->getBaseSubtotalInclTax() >= $this->getConfigData('free_shipping_minimum_subtotal'))) 
                && (($this->getConfigData('free_shipping_maximum_subtotal') == ''))) {
            $shippingPrice = '0.00';
        } else if ((($this->getConfigData('free_shipping_maximum_subtotal') != '') 
                && ($request->getBaseSubtotalInclTax() <= $this->getConfigData('free_shipping_maximum_subtotal'))) 
                && (($this->getConfigData('free_shipping_minimum_subtotal') == ''))) {
            $shippingPrice = '0.00';
        }
        return $shippingPrice;
    }

    /**
     * @param int|float $shippingPrice
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    protected function createResultMethod($shippingPrice) {
        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->_rateMethodFactory->create();

        $method->setCarrier($this->getCarrierCode());
        $method->setCarrierTitle($this->getConfigData('title'));

        /**
         * Displayed as shipping method under Carrier
         */
        $method->setMethod($this->getCarrierCode());
        $method->setMethodTitle($this->getConfigData('name'));

        $method->setPrice($shippingPrice);
        $method->setCost($shippingPrice);

        return $method;
    }

    /**
     * @param mixed $item
     * @return mixed
     */
    protected function getFreeBoxesCountFromChildren($item) {
        $freeBoxes = 0;
        foreach ($item->getChildren() as $child) {
            if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                $freeBoxes += $item->getQty() * $child->getQty();
            }
        }
        return $freeBoxes;
    }
}