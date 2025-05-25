/*
        Note that the PMPro Generic Payment Gateway plugin only loads this JS on the PMPro checkout page.
        *** Warning!!! Changed _check_ for _generic_ - NOT suere if that is correct!!!
*/

//set some vars
if ( typeof pmpro_require_billing === 'undefined' ) {
    var pmpro_require_billing;
    var pmpro_gpg_interval_handle;
}

if ( typeof code_level === 'undefined' ) {      
        var code_level;
        code_level = pmprogpg.code_level;
}

function pmprogpg_isLevelFree() {
        "use strict";
        var check_level;
        var has_variable_pricing = ( jQuery('#price').length > 0 );
        var has_donation = ( jQuery('#donation').length > 0 );

        if(typeof code_level === 'undefined' || code_level === false) {
                //no code or an invalid code was applied
                check_level = pmprogpg.nocode_level;
        } else {
                //default pmpro_level or level with current code applied
                check_level = code_level;
        }

        //check if level is paid or free
        if( false === has_variable_pricing && ( parseFloat(check_level.billing_amount) > 0 || parseFloat(check_level.initial_payment) > 0 ) ) {
                return false;
        } else if ( true === has_variable_pricing && ( parseFloat( jQuery('#price').val() ) > 0 || ( parseFloat(check_level.billing_amount) > 0 || parseFloat(check_level.initial_payment) > 0 ) ) ) {
                return false;
        } else if ( true === has_donation && ( parseFloat( jQuery('#donation').val() ) > 0 || ( parseFloat(check_level.billing_amount) > 0 || parseFloat(check_level.initial_payment) > 0 ) ) ) {
                return false;
        } else {
            return true;
    }
}

function pmprogpg_isCheckGatewayChosen() {
        if(jQuery('input[name=gateway]:checked').val() === 'generic' || pmprogpg.check_only === '1') {
                return true;
        } else {
                return false;
        }
}

function pmprogpg_isPayPalExpressChosen() {
        if(jQuery('input[name=gateway]:checked').val() == 'paypalexpress'  ) {
                return true;
        } else {
                return false;
        }
}

function pmprogpg_isPayFast() {
        if(jQuery('input[name=gateway]:checked').val() == 'payfast'  ) {
                return true;
        } else {
                return false;
        }
}

function pmprogpg_toggleCheckoutFields() {
    "use strict";
                        
        //check for free/paid
        if(pmprogpg_isLevelFree()) {
                //free, now check if using generic gateway
                jQuery('#pmpro_billing_address_fields').hide();
                jQuery('#pmpro_payment_information_fields').hide();                     
                jQuery('.pmpro_generic_instructions').hide();
                pmpro_require_billing = false;

                //hide paypal button if applicable
                if(pmprogpg.gateway === 'paypalexpress' || pmprogpg.gateway === 'paypalstandard' )
                {
                        jQuery('#pmpro_paypalexpress_checkout').hide();
                        jQuery('#pmpro_submit_span').show();
                }
        } else {
                //paid, now check if using generic gateway
                if(pmprogpg_isCheckGatewayChosen()) {
                        //paid and check
                        jQuery('#pmpro_billing_address_fields').show();
                        jQuery('#pmpro_payment_information_fields').hide();                     
                        jQuery('.pmpro_generic_instructions').show();
                        pmpro_require_billing = false;
                } else if(pmprogpg_isPayPalExpressChosen()) {
                        //paypal express
                        jQuery('#pmpro_billing_address_fields').hide();
                        jQuery('#pmpro_payment_information_fields').hide();                     
                        jQuery('#pmpro_submit_span').hide();
                        jQuery('#pmpro_paypalexpress_checkout').show();
                        jQuery('.pmpro_generic_instructions').hide();
                        pmpro_require_billing = false;
                } else if ( pmprogpg_isPayFast()) {
                        jQuery('#pmpro_billing_address_fields').hide();
                        jQuery('#pmpro_payment_information_fields').hide();                     
                        jQuery('.pmpro_generic_instructions').hide();
                        pmpro_require_billing = false;
                } else {
                        //paid and default
                        jQuery('#pmpro_billing_address_fields').show();
                        jQuery('#pmpro_payment_information_fields').show();                     
                        jQuery('.pmpro_generic_instructions').hide();
                        pmpro_require_billing = true;
                }

                //show paypal button if applicable
                if(pmprogpg.gateway === 'paypalexpress' || pmprogpg.gateway === 'paypalstandard' ) {
                        if(pmprogpg_isCheckGatewayChosen()) {
                                jQuery('#pmpro_paypalexpress_checkout').hide();
                                jQuery('#pmpro_submit_span').show();                            
                        } else {
                                jQuery('#pmpro_paypalexpress_checkout').show();
                                jQuery('#pmpro_submit_span').hide();
                        }
                }

                //Integration for PayPal Website Payments Pro.
                if ( pmprogpg.gateway == 'paypal' ) {
                        // Figure out if they selected check or not.
                        if ( pmprogpg_isCheckGatewayChosen() ) {
                                jQuery('#pmpro_paypalexpress_checkout').hide();
                                jQuery('#pmpro_submit_span').show();                            
                        } else if( pmprogpg_isPayPalExpressChosen() ) { // see if PayPal Express is selected.
                                jQuery('#pmpro_paypalexpress_checkout').show();
                                jQuery('#pmpro_submit_span').hide();
                        } else { // Revert back to defaults just in-case.
                                jQuery('#pmpro_paypalexpress_checkout').hide();
                                jQuery('#pmpro_submit_span').show();
                        }
                }

                // If only Generic Payment Gateway is chosen.
                if ( pmprogpg.gateway === 'generic' ) {
                        jQuery('#pmpro_billing_address_fields').show();
                        jQuery('#pmpro_payment_information_fields').hide();                     
                        jQuery('.pmpro_generic_instructions').show();
                        pmpro_require_billing = false;
                }
        }

        //check if billing address hide/show is overriden by filters
        if (parseInt(pmprogpg.hide_billing_address_fields) === 1) {
                jQuery('#pmpro_billing_address_fields').hide();
        }
}

function pmprogpg_togglePaymentMethodBox()  {
        "use strict";

        //check if level is paid or free
        if(pmprogpg_isLevelFree()) {
                //free
                jQuery( '#pmpro_payment_method' ).hide();
        } else {
                //not free
                jQuery( '#pmpro_payment_method' ).show();
        }

        //update checkout fields as well
        pmprogpg_toggleCheckoutFields();
}

jQuery(document).ready(function () {
        "use strict";
        //choosing payment method
        jQuery('input[name=gateway]').bind('click change keyup', function () {
                pmprogpg_toggleCheckoutFields();
        });

        //run on load
        if ( !pmprogpg.pmpro_review ) {
                pmprogpg_togglePaymentMethodBox();              
        }

});
