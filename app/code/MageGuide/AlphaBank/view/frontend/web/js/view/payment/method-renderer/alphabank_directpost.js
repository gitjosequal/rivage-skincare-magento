define(
    [
        'Magento_Checkout/js/view/payment/default',
        'jquery',
        "mage/validation"
    ],
    function (Component,$) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'MageGuide_AlphaBank/payment/alphabank_directpost'
            },
			redirectAfterPlaceOrder: false,

			afterPlaceOrder: function () {
				$.mage.redirect(window.checkoutConfig.alphabank.payment.getStartUrl);
			},
            getCode: function() {
                return 'alphabank_directpost';
            },
			isShowLegend: function(){
				return true;
			},
			isAlphabankActive: function() {
                return true;
            },
			isRadioButtonVisible:function() {
				return true;
			},
			hasAlphabankInstallment: function(){
				return window.checkoutConfig.alphabank.payment.getAlphabankInstallmentOptions;
			},
			showHideInstallments: function(){
				$("#alphabank-cc-number-of-installments").hide();
				if(this.hasAlphabankInstallment() && $('#' + this.getCode() + '_cc_should_pay_with_installments').is(":checked"))
					$("#alphabank-cc-number-of-installments").show();
				return true;
			},
			getAlphabankNumberofInstallments: function(){
				return window.checkoutConfig.alphabank.payment.getAlphabankNumberofInstallments;
			},
			getCcShouldPayInstalment: function()
			{
				if(this.hasAlphabankInstallment() && $('#' + this.getCode() + '_cc_should_pay_with_installments').is(":checked"))
				{
					return $('#' + this.getCode() + '_cc_should_pay_with_installments').val();
				}
				return 0;

			},
			getCcNumberOfInstalment: function()
			{
				if(this.hasAlphabankInstallment()  && $('#' + this.getCode() + '_cc_should_pay_with_installments').is(":checked"))
				{
					return $('#' + this.getCode() + '_number_of_installments').val();
				}
				return 0;
			},
			getData: function () {
                var parent = this._super(),
                paymentData = {};
				paymentData['cc_should_pay_with_installments'] 	= this.getCcShouldPayInstalment();
				paymentData['cc_number_of_installments'] 		= this.getCcNumberOfInstalment();

                return $.extend(true, parent, {'additional_data': paymentData});
            },
            validate: function() {
                var $form = $('#' + this.getCode() + '-form');
                return $form.validation() && $form.validation('isValid');
            },
			getImagealpha: function() {
                return window.imgpath;
            }
        });
    }
);