/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/flatrate2',
        '../../model/shipping-rates-validation-rules/flatrate2'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        sampleShippingProviderShippingRatesValidator,
        sampleShippingProviderShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('ibflatrate2', sampleShippingProviderShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('ibflatrate2', sampleShippingProviderShippingRatesValidationRules);
        return Component;
    }
);