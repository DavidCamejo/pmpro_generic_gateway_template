<?php
//      *** Warning!!! Changed _check_ for _generic_ - NOT suere if that is correct!!!

//add generic as a valid gateway
function pmprogpg_pmpro_valid_gateways($gateways)
{
    $gateways[] = "generic";
    return $gateways;
}
add_filter("pmpro_valid_gateways", "pmprogpg_pmpro_valid_gateways");

/*
        Add generic payment gateway as an option
*/
//add option to checkout along with JS
function pmprogpg_checkout_boxes()
{
    global $gateway, $pmpro_review;
    $gateway_setting = get_option("pmpro_gateway");
    $pmpro_level = pmpro_getLevelAtCheckout();

    $options = pmprogpg_getOptions($pmpro_level->id);

    $generic_gateway_label = get_option( 'pmpro_generic_gateway_label' ) ?: __( 'Generic', 'pmpro-generic-gateway' );

    //only show if the main gateway is not generic and setting value == 1 (value == 2 means only use generic payment)
    if ( $gateway_setting != "generic" && $options['setting'] == 1 ) { ?>
      <fieldset id="pmpro_payment_method" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_fieldset', 'pmpro_payment_method' ) ); ?>">
        <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card' ) ); ?>">
          <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
            <legend class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_legend' ) ); ?>">
              <h2 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_heading pmpro_font-large' ) ); ?>">
                <?php esc_html_e( 'Choose Your Payment Method', 'pmpro-generic-gateway' ); ?>
              </h2>
            </legend>
            <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_fields' ) ); ?>">
              <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio' ) ); ?>">
                <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field-radio-items pmpro_cols-2' ) ); ?>">
                <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio-item' ) ); ?> gateway_<?php echo esc_attr($gateway_setting); ?>">
                  <input type="radio" id="gateway_<?php echo esc_attr( $gateway_setting ); ?>" name="gateway" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-radio' ) ); ?>" value="<?php echo $gateway_setting;?>" <?php if(!$gateway || $gateway == $gateway_setting) { ?>checked="checked"<?php } ?> />
                  <label for="gateway_<?php echo esc_attr( $gateway_setting ); ?>" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_form_label-inline pmpro_clickable' ) ); ?>">
                    <?php if($gateway_setting == "paypalexpress" || $gateway_setting == "paypalstandard") { ?>
                            <?php _e('Pay with PayPal', 'pmpro-generic-gateway');?>
                    <?php } elseif($gateway_setting == 'twocheckout') { ?>
                            <?php _e('Pay with 2Checkout', 'pmpro-generic-gateway');?>
                    <?php } elseif( $gateway_setting == 'payfast' ) { ?>
                            <?php _e('Pay with PayFast', 'pmpro-generic-gateway');?>
                    <?php } else { ?>
                            <?php _e('Pay by Credit Card', 'pmpro-generic-gateway');?>
                    <?php } ?>
                  </label>
                </div> <!-- end pmpro_form_field pmpro_form_field-radio-item -->
                <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio-item' ) ); ?> gateway_check">
                  <input type="radio" id="gateway_check" name="gateway" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-radio' ) ); ?>" value="generic" <?php if($gateway == "generic") { ?>checked="checked"<?php } ?> />
                  <label for="gateway_check" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_form_label-inline pmpro_clickable' ) ); ?>">
                    <?php echo esc_html( sprintf( __( 'Pay by %s', 'pmpro-generic-gateway' ), $generic_gateway_label ) ); ?>
                  </label>
                </div> <!-- end pmpro_form_field pmpro_form_field-radio-item -->
                <?php
                  // Support the PayPal Website Payments Pro Gateway which has PayPal Express as a second option natively
                  if ( $gateway_setting == "paypal" ) { ?>
                    <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_field pmpro_form_field-radio-item' ) ); ?> gateway_paypalexpress">
                      <input type="radio" id="gateway_paypalexpress" name="gateway" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_input pmpro_form_input-radio' ) ); ?>" value="paypalexpress" <?php checked( 'paypalexpress', $gateway ); ?> />
                      <label for="gateway_paypalexpress" class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_form_label pmpro_form_label-inline pmpro_clickable' ) ); ?>">
                        <?php esc_html_e( 'Check Out with PayPal', 'pmpro-generic-gateway' ); ?>
                      </label>
                    </div> <!-- end pmpro_form_field pmpro_form_field-radio-item -->
                  <?php
                  }
                ?>
              </div> <!-- end pmpro_form_field-radio-items -->
              </div> <!-- end pmpro_form_field pmpro_form_field-radio -->
            </div> <!-- end pmpro_form_fields -->
          </div> <!-- end pmpro_card_content -->
        </div> <!-- end pmpro_card -->
      </fieldset> <!-- end pmpro_payment_method -->
    <?php
    } elseif ( $gateway_setting != "generic" && $options['setting'] == 2 ) { ?>
      <input type="hidden" name="gateway" value="generic" />
    <?php
    }
}
add_action("pmpro_checkout_boxes", "pmprogpg_checkout_boxes", 20);

/**
 * Toggle payment method when discount code is updated
 */
function pmprogpg_pmpro_applydiscountcode_return_js() {
    ?>
    pmprogpg_togglePaymentMethodBox();
    <?php
}
add_action('pmpro_applydiscountcode_return_js', 'pmprogpg_pmpro_applydiscountcode_return_js');

/**
 * Enqueue scripts on the frontend.
 */
function pmprogpg_enqueue_scripts() {

    if(!function_exists('pmpro_getLevelAtCheckout'))
      return;
    
    global $gateway, $pmpro_review, $pmpro_pages, $post, $pmpro_msg, $pmpro_msgt;

    // If post not set, bail.
    if( ! isset( $post ) ) {
        return;
    }

    //make sure we're on the checkout page
    if(!is_page($pmpro_pages['checkout']) && !empty($post) && strpos($post->post_content, "[pmpro_checkout") === false)
        return;

    wp_register_script('pmpro-generic-gateway', plugins_url( 'js/pmpro-generic-gateway.js', PMPRO_GENERIC_GATEWAY_BASE_FILE ), array( 'jquery' ), PMPROPGP_VER );

    //store original msg and msgt values in case these function calls below affect them
    $omsg = $pmpro_msg;
    $omsgt = $pmpro_msgt;

    //get original checkout level and another with discount code applied    
    $pmpro_nocode_level = pmpro_getLevelAtCheckout(false, '^*NOTAREALCODE*^');
    $pmpro_code_level = pmpro_getLevelAtCheckout();                 //NOTE: could be same as $pmpro_nocode_level if no code was used

    // Determine whether this level is a "generic only" level.
    $generic_only = 0;
    if ( ! empty( $pmpro_code_level->id ) ) {
        $options = pmprogpg_getOptions( $pmpro_code_level->id );
        if ( $options['setting'] == 2 ) {
            $generic_only = 1;
        }
    }

    //restore these values
    $pmpro_msg = $omsg;
    $pmpro_msgt = $omsgt;
    
    wp_localize_script('pmpro-generic-gateway', 'pmprogpg', array(
        'gateway' => get_option('pmpro_gateway'),
        'nocode_level' => $pmpro_nocode_level,
        'code_level' => $pmpro_code_level,
        'pmpro_review' => (bool)$pmpro_review,
        'is_admin'  =>  is_admin(),
        'hide_billing_address_fields' => apply_filters('pmpro_hide_billing_address_fields', false ),
        'generic_only' => $generic_only,
        )
    );

    wp_enqueue_script('pmpro-generic-gateway');

}
add_action("wp_enqueue_scripts", 'pmprogpg_enqueue_scripts');

/*
        Need to remove some filters added by the generic gateway.
        The default gateway will have it's own idea RE this.
*/
function pmprogpg_init_include_billing_address_fields( $level = null) {
        //make sure PMPro is active
        if(!function_exists('pmpro_getGateway'))
                return;

        //billing address and payment info fields
        if ( empty( $level ) ) {
                $level = pmpro_getLevelAtCheckout();
        }

        if ( ! empty( $level->id ) )
        {
                $options = pmprogpg_getOptions( $level->id );
                        
                if($options['setting'] == 2)
                {
                        //Only hide the address if we're not using the Address for Free Levels Add On
                        if ( ! function_exists( 'pmproaffl_pmpro_required_billing_fields' ) ) {                         
                                //hide billing address and payment info fields
                                add_filter('pmpro_include_billing_address_fields', '__return_false', 20);
                                add_filter('pmpro_include_payment_information_fields', '__return_false', 20);
                        }

                        // Need to also specifically remove them for Stripe.
                        remove_filter( 'pmpro_include_payment_information_fields', array( 'PMProGateway_stripe', 'pmpro_include_payment_information_fields' ) );

                        //Hide the toggle section if the PayPal Express Add On is active
                        remove_action( "pmpro_checkout_boxes", "pmproappe_pmpro_checkout_boxes", 20 );
                } else {
                        //keep paypal buttons, billing address fields/etc at checkout
                        $default_gateway = get_option('pmpro_gateway');
                        if($default_gateway == 'paypalexpress') {
                                add_filter('pmpro_checkout_default_submit_button', array('PMProGateway_paypalexpress', 'pmpro_checkout_default_submit_button'));
                                if ( version_compare( PMPRO_VERSION, '2.1', '>=' ) ) {
                                        add_action( 'pmpro_checkout_preheader', array( 'PMProGateway_paypalexpress', 'pmpro_checkout_preheader' ) );
                                } else {
                                        /**
                                         * @deprecated No longer used since paid-memberships-pro v2.1
                                         */
                                        add_action( 'pmpro_checkout_after_form', array( 'PMProGateway_paypalexpress', 'pmpro_checkout_after_form' ) );
                                }
                        } elseif($default_gateway == 'paypalstandard') {
                                add_filter('pmpro_checkout_default_submit_button', array('PMProGateway_paypalstandard', 'pmpro_checkout_default_submit_button'));
                        } elseif($default_gateway == 'paypal') {
                                if ( version_compare( PMPRO_VERSION, '2.1', '>=' ) ) {
                                        add_action( 'pmpro_checkout_preheader', array( 'PMProGateway_paypal', 'pmpro_checkout_preheader' ) );
                                } else {
                                        /**
                                         * @deprecated No longer used since paid-memberships-pro v2.1
                                         */
                                        add_action( 'pmpro_checkout_after_form', array( 'PMProGateway_paypal', 'pmpro_checkout_after_form' ) );
                                }
                                add_filter('pmpro_include_payment_option_for_paypal', '__return_false');
                        } elseif($default_gateway == 'twocheckout') {
                                //undo the filter to change the checkout button text
                                remove_filter('pmpro_checkout_default_submit_button', array('PMProGateway_twocheckout', 'pmpro_checkout_default_submit_button'));
                        } else if( $default_gateway == 'payfast' ) {
                                add_filter( 'pmpro_include_billing_address_fields', '__return_false' ); 
                        } else {                                
                                //onsite checkouts

                                //the generic gateway class in core adds filters like these
                                remove_filter( 'pmpro_include_billing_address_fields', '__return_false' );
                                remove_filter( 'pmpro_include_payment_information_fields', '__return_false' );
                                
                                //make sure the default gateway is loading their billing address fields
                                if(class_exists('PMProGateway_' . $default_gateway) && method_exists('PMProGateway_' . $default_gateway, 'pmpro_include_billing_address_fields')) {
                                        add_filter('pmpro_include_billing_address_fields', array('PMProGateway_' . $default_gateway, 'pmpro_include_billing_address_fields'));
                                }                                       
                        }                       
                }
        }

        //instructions at checkout
        remove_filter('pmpro_checkout_after_payment_information_fields', array('PMProGateway_generic', 'pmpro_checkout_after_payment_information_fields'));
        add_filter('pmpro_checkout_after_payment_information_fields', 'pmprogpg_pmpro_checkout_after_payment_information_fields');              
}
add_action( 'pmpro_checkout_preheader_after_get_level_at_checkout', 'pmprogpg_init_include_billing_address_fields', 20, 1 );

/**
 * Cancels all previously pending check orders if a user purchases the same level via a different payment method.
 * 
 * @since 0.11
 */
function pmprogpg_cancel_previous_pending_orders( $user_id, $order ) {
        global $wpdb;

        // Update any outstanding generic payments for this level ID.
        $wpdb->query(
                $wpdb->prepare(
                        "UPDATE $wpdb->pmpro_membership_orders
                        SET `status` = 'error'
                        WHERE `user_id` = %d
                        AND `gateway` = 'generic'
                        AND `status` = 'pending'
                        AND `membership_id` = %d",
                        $user_id,
                        $order->membership_id
                )
        );
}
add_action( 'pmpro_after_checkout', 'pmprogpg_cancel_previous_pending_orders', 10, 2 );

/*
        Show instructions on the checkout page.
*/
function pmprogpg_pmpro_checkout_after_payment_information_fields() {
        global $gateway;
        $pmpro_level = pmpro_getLevelAtCheckout();

        $options = pmprogpg_getOptions($pmpro_level->id);

        if( !empty($options) && $options['setting'] > 0 ) {
                $instructions = get_option("pmpro_instructions");
                $generic_gateway_label = get_option( 'pmpro_generic_gateway_label' );
                ?>
                <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card pmpro_generic_instructions', 'pmpro_generic_instructions' ) ); ?>" <?php echo $gateway != 'generic' ? 'style="display:none;"' : ''; ?>>
                  <h2 class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_title pmpro_font-large' ) ); ?>"><?php echo esc_html( sprintf( __( 'Pay by %s', 'pmpro-generic-gateway' ), $generic_gateway_label ) ); ?></h2>
                  <div class="<?php echo esc_attr( pmpro_get_element_class( 'pmpro_card_content' ) ); ?>">
                    <?php echo wp_kses_post( wpautop( wp_unslash( $instructions ) ) ); ?>
                  </div> <!-- end pmpro_card_content -->
                </div> <!-- end pmpro_generic_instructions -->
                <?php
        }
}

/**
 * Make sure that orders have the "generic" gateway if
 * the level is set to "generic only".
 *
 * @since 1.1.3
 *
 * @param MemberOrder $order The checkout order object.
 * @return MemberOrder
 */
function pmprogpg_checkout_order( $order ) {
        // Check if this level is generic only.
        $options = pmprogpg_getOptions( $order->membership_id );
        if ( $options['setting'] == 2 ) {
                // This is a generic only level. Make sure that the order is set to the generic gateway.
                $order->setGateway( 'generic' );
        }

        return $order;
}
add_filter( 'pmpro_checkout_order', 'pmprogpg_checkout_order' );

/**
 * When getting the gateway object for a "generic" order/subscription, swap it
 * for our custom "generic" gateway.
 *
 * Will only run for PMPro v3.0.3+.
 *
 * @since 1.0
 *
 * @param PMProGateway
 * @return PMProGateway
 */
function pmprogpg_use_custom_gateway_class( $gateway ) {
        // If the passed gateway is not the generic gateway, bail.
        if ( ! is_a( $gateway, 'PMProGateway_generic' ) ) {
                return $gateway;
        }

        // Swap the gateway object for our custom gateway object.
        require_once PMPRO_GENERIC_GATEWAY_DIR . '/classes/class.pmprogateway_gpg.php';
        return new PMProGateway_gpg();
}
add_filter( 'pmpro_order_gateway_object', 'pmprogpg_use_custom_gateway_class' );
add_filter( 'pmpro_subscription_gateway_object', 'pmprogpg_use_custom_gateway_class' );

/**
 * set check generic to pending until they are paid
 * This filter is only run for PMPro versions earlier than 3.0.3 since we are overwriting the core gateway in 3.0.3+.
 */
function pmprogpg_pmpro_generic_status_after_checkout($status) {
        return 'pending';
}
add_filter( 'pmpro_generic_status_after_checkout', 'pmprogpg_pmpro_generic_status_after_checkout' );

/**
 * Whenever a generic order is saved, we need to update the subscription data.
 *
 * @param MemberOrder $morder - Updated order as it's being saved
 */
function pmprogpg_update_subscription_data_for_order( $morder ) {
        // Only worry about this if this is a generic order.
        if ( "generic" !== $morder->gateway ) {
                return;
        }

        // If using PMPro v3.0+, update the subscription data.
        if ( method_exists( $morder, 'get_subscription' ) ) {
                $subscription = $morder->get_subscription();
                if ( ! empty( $subscription ) ) {
                        $subscription->update();
                }
        }
}
add_action( 'pmpro_added_order', 'pmprogpg_update_subscription_data_for_order', 10, 1 );
add_action( 'pmpro_updated_order', 'pmprogpg_update_subscription_data_for_order', 10, 1 );

/**
 * Send Invoice to user if/when changing order status to "success" for Generic based payment.
 * Also processes checkout if the order was a delayed checkout order.
 *
 * @param MemberOrder $morder - Updated order as it's being saved
 */
function pmprogpg_order_status_success( $morder ) {
    // Only worry about this if this is a generic order.
    if ( "generic" !== strtolower( $morder->gateway ) ) {
                return;
        }

        // Check if we are switching to success status.
        if ( 'success' !== $morder->status || 'success' === $morder->original_status) {
                return;
        }

        // Check if the order was a chekout order.
        $checkout_request_vars = get_pmpro_membership_order_meta( $morder->id, 'checkout_request_vars', true );
        if ( ! empty( $checkout_request_vars ) ) {
                // Process the checkout and avoid infinite loops. This should send the checkout email.
                $original_request_vars = $_REQUEST;
                pmpro_pull_checkout_data_from_order( $morder );
                remove_action( 'pmpro_update_order', 'pmprogpg_order_status_success', 10, 1 );
                pmpro_complete_async_checkout( $morder );
                add_action( 'pmpro_update_order', 'pmprogpg_order_status_success', 10, 1 );
                $_REQUEST = $original_request_vars;
        } else {
                // Send an invoice email for the order.
                $recipient = get_user_by( 'ID', $morder->user_id );
                $invoice_email = new PMProEmail();
                $invoice_email->sendInvoiceEmail( $recipient, $morder );

                // Update the subscription for this order if needed.
                pmprogpg_update_subscription_data_for_order( $morder );
        }
}
add_action( 'pmpro_update_order', 'pmprogpg_order_status_success', 10, 1 );
