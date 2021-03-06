define(
    [
        'jquery',
        "underscore",
        'uiComponent',
        'ko',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer',
        'Magento_Customer/js/model/address-list',
        'Magento_Checkout/js/model/address-converter',
        'Magento_Checkout/js/action/select-shipping-address',
        'Magento_Checkout/js/model/payment-service',
        'Magento_Checkout/js/model/payment/method-converter',
        'Magento_Checkout/js/action/get-payment-information',
        'Magento_Checkout/js/model/checkout-data-resolver',
        'mage/translate',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Checkout/js/model/payment/method-list',
        'Magento_Checkout/js/model/payment/additional-validators',
        'uiRegistry',
        'IWD_Opc/js/model/payment/is-loading',
        'Magento_Checkout/js/action/set-billing-address',
        'Magento_Ui/js/model/messageList',
        'mage/validation'
    ],
    function ($,
              _,
              Component,
              ko,
              quote,
              customer,
              addressList,
              addressConverter,
              selectShippingAddress,
              paymentService,
              methodConverter,
              getPaymentInformation,
              checkoutDataResolver,
              $t,
              checkoutData,
              fullScreenLoader,
              methodList,
              additionalValidators,
              registry,
              paymentIsLoading,
              setBillingAddressAction,
              globalMessageList) {
        'use strict';

        /** Set payment methods to collection */
        paymentService.setPaymentMethods(methodConverter(window.checkoutConfig.paymentMethods));

        $.validator.addMethod('validate-iwd-opc-cc-exp', function (value, element) {
            var isValid = false;
            var monthField = $(element).closest('.field').find('select[name="payment[cc_exp_month]"]');
            var yearField = $(element).closest('.field').find('select[name="payment[cc_exp_year]"]');
            if (monthField.length && yearField.length) {
                isValid = monthField.val() && yearField.val();
            }

            return isValid;
        }, 'This is a required field.');

        return Component.extend({
            defaults: {
                template: 'IWD_Opc/payment',
                activeMethod: ''
            },
            isLoading: paymentIsLoading.isLoading,
            isShowSubscribe: quote.isShowSubscribe(),
            isSubscribe: ko.observable(checkoutData.getIsSubscribe()),
            quoteIsVirtual: quote.isVirtual(),
            isPaymentMethodsAvailable: ko.computed(function () {
                return paymentService.getAvailablePaymentMethods().length > 0;
            }),
            isPlaceOrderActionAllowed: ko.observable(true),

            initialize: function () {
                this._super();
                this.navigate();
                var self = this;
                this.isSubscribe.subscribe(function (value) {
                    checkoutData.setIsSubscribe(value);
                });
                $(document).on('mouseover', '.iwd_opc_cc_types_tooltip', function () {
                    var fixedContent = $(this).find('.iwd_opc_field_tooltip_content');
                    if (fixedContent.length) {
                        fixedContent.offset({
                            left: $(this).offset().left - fixedContent.width() - 44,
                            top: $(this).offset().top - (fixedContent.height() / 2) + 2
                        });
                    }
                });

                $(document).on('input', '.iwd_opc_cc_wrapper .iwd_opc_cc_input', function (e) {
                    self.formatCcNumber($(this), e);
                    var v = $(this).val().replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    self.fillCcNumberField($(this), v);
                });

                $(document).on('keydown', '.iwd_opc_cc_exp, .iwd_opc_cc_wrapper .iwd_opc_cc_input', function (e) {
                    self.checkNumberInput($(this), e);
                });

                $(document).on('input', '.iwd_opc_cc_exp', function (e) {
                    var value = $(this).val();
                    if (value.length > 2 && value[2] !== '/') {
                        var cursorPosition = $(this).prop('selectionStart');
                        value = value.replace('/', '');
                        var month = value.substring(0, 2);
                        var year = value.substring(2);
                        value = month + '/' + year;
                        value = value.substring(0, 7);
                        $(this).val(value);
                        $(this).selectRange(cursorPosition + 1);
                    }

                    var ccExpMonthYearArr = value.split('/');
                    var is_valid = false;
                    if (ccExpMonthYearArr[0] && ccExpMonthYearArr[1]) {
                        var ccExpMonth = parseInt(ccExpMonthYearArr[0]);
                        var ccExpYear = parseInt(ccExpMonthYearArr[1]);
                        var currentTime = new Date();
                        var currentMonth = currentTime.getMonth() + 1;
                        var currentYear = currentTime.getFullYear();
                        if (ccExpMonth > 12) {
                            is_valid = false;
                        } else if (ccExpYear > currentYear) {
                            is_valid = true;
                        } else if (ccExpMonth >= currentMonth && ccExpYear === currentYear) {
                            is_valid = true;
                        }

                        if (is_valid) {
                            self.fillCcExpDateFields($(this), ccExpMonth, ccExpYear);
                        }
                    }

                    if (!is_valid) {
                        self.fillCcExpDateFields($(this), '', '');
                    }
                });

                return this;
            },
            formatCcNumber: function (element, e) {
                var value = element.val();
                if (value) {
                    var clear_value = value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
                    var formatted_value = clear_value;
                    var cursorPosition = element.prop('selectionStart');
                    var matches = clear_value.match(/\d{4,24}/g);
                    var match = matches && matches[0] || '';
                    var parts = [];
                    for (var i = 0; i < match.length; i += 4) {
                        parts.push(match.substring(i, i + 4))
                    }

                    if (parts.length) {
                        formatted_value = parts.join(' ');
                        var test_value = value.substring(0, cursorPosition);
                        var test_formatted_value = formatted_value.substring(0, cursorPosition);
                        var test_value_length = test_value.match(/ /g) ? test_value.match(/ /g).length : 0;
                        var test_formatted_value_length = test_formatted_value.match(/ /g) ? test_formatted_value.match(/ /g).length : 0;
                        if (test_formatted_value_length > test_value_length) {
                            cursorPosition = cursorPosition + test_formatted_value_length - test_value_length;
                        }
                    }

                    if ((cursorPosition % 5) === 0) {
                        cursorPosition = cursorPosition - 1;
                    }

                    element.val(formatted_value);
                    element.selectRange(cursorPosition);
                }
            },

            fillCcNumberField: function (element, value) {
                var ccNumberField = element.closest('.field').find('input.iwd_opc_cc_real_input');
                if (ccNumberField.length) {
                    ccNumberField.val(value);
                    ccNumberField.trigger('keyup');
                    ccNumberField.trigger('value');
                    ccNumberField.trigger('input');
                }
            },

            fillCcExpDateFields: function (element, expMonth, expYear) {
                var monthField = element.closest('.field').find('select[name="payment[cc_exp_month]"]');
                var yearField = element.closest('.field').find('select[name="payment[cc_exp_year]"]');

                if (monthField.length && yearField.length) {
                    monthField.val(expMonth).trigger('change');
                    yearField.val(expYear).trigger('change');
                }
            },

            checkNumberInput: function (element, e) {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    e.ctrlKey === true ||
                    e.shiftKey === true ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true) ||
                    (e.keyCode >= 35 && e.keyCode <= 39)) {
                    return true;
                } else if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                    return false;
                }

                return null;
            },
            navigate: function () {
                var self = this;
                paymentIsLoading.isLoading(true);
                getPaymentInformation().always(function () {
                    paymentIsLoading.isLoading(false);
                });
            },

            getFormKey: function () {
                return window.checkoutConfig.formKey;
            },

            placeOrder: function (data, event) {
                var self = this;
                this.isPlaceOrderActionAllowed(false);
                if (event) {
                    event.preventDefault();
                }
                // if validate 
                if(parseInt($("#checkout_flag").val()) == 1){
                    if (additionalValidators.validate()) {
                        if (!quote.isVirtual()) {
                            var shippingAddress = registry.get('checkout.steps.shipping-step.shippingAddress');
                            shippingAddress.setShippingInformation().done(function () {
                            localStorage.setItem('custom_attributes',JSON.stringify(shippingAddress.source.shippingAddress));			
                            self.clickNativePlaceOrder();
                            }).fail(function () {
                                self.isPlaceOrderActionAllowed(true);
                            });
                        } else {
                            if (quote.paymentMethod() && quote.paymentMethod().method === 'braintree_paypal') {
                                self.clickNativePlaceOrder();
                            } else {
                                setBillingAddressAction(globalMessageList).done(function () {
                                    self.clickNativePlaceOrder();
                                }).fail(function () {
                                    self.isPlaceOrderActionAllowed(true);
                                });
                            }
                        }
                    } 
                    else 
                    {
                       this.isPlaceOrderActionAllowed(true);
                    }
                } // end of if
                else { // start of else  
                    if (customer.isLoggedIn()) {
                        var $num = '';
                        if(quote.shippingAddress().getType() == 'new-customer-address') { 
                            var dataForm = $('#co-shipping-form');
                                $num = $("input[name=telephone]").val();
                                var text ='';
                                if($num.length >= 10 ){// $num has + with countrt code
                                    var s2 = $num.substr(1);
                                   // text = 971+s2;
                                    if($(".customer_ph_number").val().substr(0, 2) != '05') {
                                        $(".customer_ph_number").after('<span style="color:red" class="error">Phone Number is invalid must starts with 05.</span>');
                                        $(".customer_ph_number").focus();
                                        $('.iwd_opc_place_order_button').removeAttr("disabled");
                                    }
                                    else { renderallOTP(); }
                                }
                                else {// worong format
                                    $("#modal-title-0").text('Invalid Number');
                                    // alert("Number not Valid");
                                    $(".customer_ph_number").after('<span style="color:red" class="error">Phone Number is invalid minimum length is 10.</span>');
                                    $(".customer_ph_number").focus();
                                    $('.iwd_opc_place_order_button').removeAttr("disabled");
                                    return false;
                                }
                                
                                function renderallOTP() {
                                /*_________ For Sending SMS Send ajax request ________ */  
                                  
                                $.ajax({
                                headers: { "Accept": "application/json"},
                                type: 'POST',
                                url: 'https://www.thepetstore.ae/sms.php?method=1&phone='+$num+'&event=Checkout',
                                crossDomain: true,
                                beforeSend: function(xhr){
                                    xhr.withCredentials = true;
                                },
                                success: function(data, textStatus, request){
                                    // $(".modal-title").text("OPT have sent to You");
                                    $("#otp_send_confirm").text("An OTP (One Time Passcode) has been sent to");
                                    $("#mobileNumber_otp").text($num + '. ' + 'Please enter the OTP in the field below to verify.');
                                    
                                    var timeLeft = 120;
                                    var elem = document.getElementById('modal-title-0');
                                    var timer_elem = document.getElementById('timer_run');
                                    $("#opt_SMS").attr("disabled", true);
                                    // var timerId = setInterval(countdown, 1000);
                                    var timerId;
                                    timerId = setInterval(countdown, 1000);
                                    var timeLeft = document.getElementById('counter_timer').value;
                                    
                                    function countdown() {
                                        if (timeLeft == 0) {
                                            clearTimeout(timerId);
                                            timer_elem.innerHTML = 'Your OTP Code is Expire';
                                            document.getElementById('counter_timer').value = timeLeft;
                                        $("#opt_SMS").removeAttr("disabled");
                                        } else {
                                            timer_elem.innerHTML = timeLeft + ' seconds remaining';
                                            timeLeft--;
                                            document.getElementById('counter_timer').value = timeLeft;
                                        }
                                    }
            
                                    console.log(data);
            
                                },error: function (jqXHR, exception) {
                                    var msg = '';
                                    if (jqXHR.status === 0) {
                                        msg = 'Not connect.\n Verify Network.';
                                    } else if (jqXHR.status == 404) {
                                        msg = 'Requested page not found. [404]';
                                    } else if (jqXHR.status == 500) {
                                        msg = 'Internal Server Error [500].';
                                    } else if (exception === 'parsererror') {
                                        msg = 'Requested JSON parse failed.';
                                    } else if (exception === 'timeout') {
                                        msg = 'Time out error.';
                                    } else if (exception === 'abort') {
                                        msg = 'Ajax request aborted.';
                                    } else {
                                        msg = 'Uncaught Error.\n' + jqXHR.responseText;
                                    }
                                    console.log(msg);
                                }
                                });
                                /*_________ For Sending SMS Send ajax request ________ */
                                $('#myModel').modal('openModal');
                                $('.iwd_opc_place_order_button').removeAttr("disabled");
                                // return false; 
                            }  
                         // }
                        } 
                        // if it is not new adddress then go ahead for checkout
                        else {
                                $('.iwd_opc_place_order_button').removeAttr("disabled");
                                if (additionalValidators.validate()) {
                                    if (!quote.isVirtual()) {
                                        var shippingAddress = registry.get('checkout.steps.shipping-step.shippingAddress');
                                        shippingAddress.setShippingInformation().done(function () {
                                        localStorage.setItem('custom_attributes',JSON.stringify(shippingAddress.source.shippingAddress));	
                                        self.clickNativePlaceOrder();
                                        }).fail(function () {
                                            self.isPlaceOrderActionAllowed(true);
                                        });
                                    } else {
                                        if (quote.paymentMethod() && quote.paymentMethod().method === 'braintree_paypal') {
                                            self.clickNativePlaceOrder();
                                        } else {
                                            setBillingAddressAction(globalMessageList).done(function () {
                                                self.clickNativePlaceOrder();
                                            }).fail(function () {
                                                self.isPlaceOrderActionAllowed(true);
                                            });
                                        }
                                    }
                                } else {
                                    this.isPlaceOrderActionAllowed(true);
                                }
                            }
                    } // if it is logged in
                    else {
                        // alert('You must need to login first.');
                        $('.block-authentication').modal('openModal');
                    }  
                } // end of else 
            },
            clickNativePlaceOrder: function () {
                this.isPlaceOrderActionAllowed(true);
                $('.payment-method._active button[type=submit].checkout').click();
            }
        });
    }
);
