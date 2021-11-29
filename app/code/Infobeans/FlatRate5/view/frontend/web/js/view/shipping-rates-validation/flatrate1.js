/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/flatrate1',
        '../../model/shipping-rates-validation-rules/flatrate1'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        sampleShippingProviderShippingRatesValidator,
        sampleShippingProviderShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('ibflatrate1', sampleShippingProviderShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('ibflatrate1', sampleShippingProviderShippingRatesValidationRules);
        return Component;
    }
);