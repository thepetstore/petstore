/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/flatrate5',
        '../../model/shipping-rates-validation-rules/flatrate5'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        sampleShippingProviderShippingRatesValidator,
        sampleShippingProviderShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('ibflatrate5', sampleShippingProviderShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('ibflatrate5', sampleShippingProviderShippingRatesValidationRules);
        return Component;
    }
);