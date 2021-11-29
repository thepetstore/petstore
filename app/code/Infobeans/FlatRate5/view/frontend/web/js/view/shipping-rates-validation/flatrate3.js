/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/flatrate3',
        '../../model/shipping-rates-validation-rules/flatrate3'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        sampleShippingProviderShippingRatesValidator,
        sampleShippingProviderShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('ibflatrate3', sampleShippingProviderShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('ibflatrate3', sampleShippingProviderShippingRatesValidationRules);
        return Component;
    }
);