<?php

/**
 * Add the Generic Payment Gateway email templates to the Core PMPro Email Templates.
 * 
 * @param array $template - The existing PMPro Email Templates.
 * @return array $template - The updated PMPro Email Templates.
 * @since 1.0.

        *** Warning!!! Changed _check_ for _generic_ - NOT suere if that is correct!!!
 */
function pmprogpg_email_template_to_pmproet_add_on( $template ) {

        $template['generic_pending'] = array(
                'subject'     => 'New Invoice for !!display_name!! at !!sitename!!',
                'description' => 'Generic Payment Gateway - Generic Pending',
                'body'        => __( '<p>You have a new invoice for !!sitename!!.</p>

                !!instructions!!
                
                <p>Below are details about your membership account and a receipt for your membership invoice.</p>
                
                <p>Account: !!display_name!! (!!user_email!!)</p>
                <p>Membership Level: !!membership_level_name!!</p>
                
                <p>
                        Invoice #!!invoice_id!! on !!invoice_date!!<br />
                        Total Billed: !!invoice_total!!
                </p>
                
                <p>Log in to your membership account here: !!login_link!!</p>', 'pmpro_generic_gateway' ),
        );
        $template['generic_pending_admin'] = array(
                'subject'     => 'Pending checkout for !!display_name!! at !!sitename!!',
                'description' => 'Generic Payment Gateway - Generic Pending Admin',
                'body'        => __( '<p>There is a pending checkout at !!sitename!!.</p>
                
                <p>Account: !!display_name!! (!!user_email!!)</p>
                <p>Membership Level: !!membership_level_name!!</p>

                <p>
                        Invoice #!!invoice_id!! on !!invoice_date!!<br />
                        Total Billed: !!invoice_total!!
                </p>', 'pmpro_generic_gateway' ),
        );
        $template['generic_pending_reminder'] = array(
                'subject'     => 'Reminder: New Invoice for !!display_name!! at !!sitename!!',
                'description' => 'Generic Payment Gateway - Generic Pending Reminder',
                'body'        => __( '<p>This is a reminder. You have a new invoice for !!sitename!!.</p>

                !!instructions!!

                <p>Below are details about your membership account and a receipt for your membership invoice.</p>
                
                <p>Account: !!display_name!! (!!user_email!!)</p>
                <p>Membership Level: !!membership_level_name!!</p>

                <p>
                        Invoice #!!invoice_id!! on !!invoice_date!!<br />
                        Total Billed: !!invoice_total!!
                </p>

                <p>Log in to your membership account here: !!login_link!!</p>', 'pmpro_generic_gateway' ),
        );

        return $template;
}
add_filter( 'pmproet_templates', 'pmprogpg_email_template_to_pmproet_add_on' );

/**
 * Send the generic_pending email.
 *
 * @param MemberOrder $order - The order object.
 * @return bool - True if the email was sent, false otherwise.
 */
function pmprogpg_send_generic_pending_email( $order ) {
        // Get the user.
        $user = get_userdata( $order->user_id );
        if ( empty( $user ) ) {
                return false;
        }

        // Get the membership level.
        $level = $order->getMembershipLevel();
        if ( empty( $level ) ) {
                return false;
        }

        $email = new PMProEmail();
        $email->template = "generic_pending";
        $email->email = $user->user_email;
        $email->subject = sprintf(__("New Invoice for %s at %s", "pmpro-generic-gateway"), $level->name, get_option("blogname"));

        //setup more data
        $email->data = array(
                "name" => $user->display_name,
                "user_login" => $user->user_login,
                "sitename" => get_option("blogname"),
                "siteemail" => pmpro_getOption("from_email"),
                "membership_id" => $level->id,
                "membership_level_name" => $level->name,
                "membership_cost" => pmpro_getLevelCost( $level ),
                "login_link" => wp_login_url(pmpro_url("account")),
                "display_name" => $user->display_name,
                "user_email" => $user->user_email,
        );

        $email->data["instructions"] = wp_unslash(  pmpro_getOption('instructions') );
        $email->data["invoice_id"] = $order->code;
        $email->data["invoice_total"] = pmpro_formatPrice($order->total);
        $email->data["invoice_date"] = date(get_option('date_format'), $order->timestamp);
        $email->data["billing_name"] = $order->billing->name;
        $email->data["billing_street"] = $order->billing->street;
        $email->data["billing_city"] = $order->billing->city;
        $email->data["billing_state"] = $order->billing->state;
        $email->data["billing_zip"] = $order->billing->zip;
        $email->data["billing_country"] = $order->billing->country;
        $email->data["billing_phone"] = $order->billing->phone;
        $email->data["cardtype"] = $order->cardtype;
        $email->data["accountnumber"] = hideCardNumber($order->accountnumber);
        $email->data["expirationmonth"] = $order->expirationmonth;
        $email->data["expirationyear"] = $order->expirationyear;
        $email->data["billing_address"] = pmpro_formatAddress($order->billing->name,
                                          $order->billing->street,
                                          "", //address 2
                                          $order->billing->city,
                                          $order->billing->state,
                                          $order->billing->zip,
                                          $order->billing->country,
                                          $order->billing->phone);

        if($order->getDiscountCode())
                $email->data["discount_code"] = "<p>" . __("Discount Code", "pmpro-generic-gateway") . ": " . $order->discount_code->code . "</p>\n";
        else
                $email->data["discount_code"] = "";

        //send the email
        return $email->sendEmail();
}

/**
 * Send the generic_pending_admin email.
 *
 * @since 1.1
 *
 * @param MemberOrder $order - The order object.
 * @return bool - True if the email was sent, false otherwise.
 */
function pmprogpg_send_generic_pending_admin_email( $order ) {
        // Get the user.
        $user = get_userdata( $order->user_id );
        if ( empty( $user ) ) {
                return false;
        }

        // Get the membership level.
        $level = $order->getMembershipLevel();
        if ( empty( $level ) ) {
                return false;
        }

        $email = new PMProEmail();
        $email->template = "generic_pending_admin";
        $email->email = get_bloginfo("admin_email");
        $email->subject = sprintf(__("Pending checkout for !!display_name!! at !!sitename!!", "pmpro-generic-gateway"), $level->name, get_option("blogname"));

        //setup more data
        $email->data = array(
                "name" => $user->display_name,
                "user_login" => $user->user_login,
                "sitename" => get_option("blogname"),
                "siteemail" => pmpro_getOption("from_email"),
                "membership_id" => $level->id,
                "membership_level_name" => $level->name,
                "membership_cost" => pmpro_getLevelCost( $level ),
                "login_link" => wp_login_url(pmpro_url("account")),
                "display_name" => $user->display_name,
                "user_email" => $user->user_email,
        );

        $email->data["instructions"] = wp_unslash(  pmpro_getOption('instructions') );
        $email->data["invoice_id"] = $order->code;
        $email->data["invoice_total"] = pmpro_formatPrice($order->total);
        $email->data["invoice_date"] = date(get_option('date_format'), $order->timestamp);
        $email->data["billing_name"] = $order->billing->name;
        $email->data["billing_street"] = $order->billing->street;
        $email->data["billing_city"] = $order->billing->city;
        $email->data["billing_state"] = $order->billing->state;
        $email->data["billing_zip"] = $order->billing->zip;
        $email->data["billing_country"] = $order->billing->country;
        $email->data["billing_phone"] = $order->billing->phone;
        $email->data["cardtype"] = $order->cardtype;
        $email->data["accountnumber"] = hideCardNumber($order->accountnumber);
        $email->data["expirationmonth"] = $order->expirationmonth;
        $email->data["expirationyear"] = $order->expirationyear;
        $email->data["billing_address"] = pmpro_formatAddress($order->billing->name,
                                          $order->billing->street,
                                          "", //address 2
                                          $order->billing->city,
                                          $order->billing->state,
                                          $order->billing->zip,
                                          $order->billing->country,
                                          $order->billing->phone);

        if($order->getDiscountCode())
                $email->data["discount_code"] = "<p>" . __("Discount Code", "pmpro-generic-gateway") . ": " . $order->discount_code->code . "</p>\n";
        else
                $email->data["discount_code"] = "";

        //send the email
        return $email->sendEmail();
}

/**
 * Send the generic_pending_reminder email.
 *
 * @param MemberOrder $order - The order object.
 * @return bool - True if the email was sent, false otherwise.
 */
function pmprogpg_send_generic_pending_reminder_email( $order ) {
        // Get the user.
        $user = get_userdata( $order->user_id );
        if ( empty( $user ) ) {
                return false;
        }

        // Get the membership level.
        $level = $order->getMembershipLevel();
        if ( empty( $level ) ) {
                return false;
        }

        $email = new PMProEmail();
        $email->template = "generic_pending_reminder";
        $email->email = $user->user_email;
        $email->subject = sprintf(__("Reminder: New Invoice for %s at %s", "pmpro-generic-gateway"), $level->name, get_option("blogname"));

        //setup more data
        $email->data = array(
                "name" => $user->display_name,
                "user_login" => $user->user_login,
                "sitename" => get_option("blogname"),
                "siteemail" => pmpro_getOption("from_email"),
                "membership_id" => $level->id,
                "membership_level_name" => $level->name,
                "membership_cost" => pmpro_getLevelCost( $level ),
                "login_link" => wp_login_url(pmpro_url("account")),
                "display_name" => $user->display_name,
                "user_email" => $user->user_email,
        );

        $email->data["instructions"] = wp_unslash(  pmpro_getOption('instructions') );
        $email->data["invoice_id"] = $order->code;
        $email->data["invoice_total"] = pmpro_formatPrice($order->total);
        $email->data["invoice_date"] = date(get_option('date_format'), $order->timestamp);
        $email->data["billing_name"] = $order->billing->name;
        $email->data["billing_street"] = $order->billing->street;
        $email->data["billing_city"] = $order->billing->city;
        $email->data["billing_state"] = $order->billing->state;
        $email->data["billing_zip"] = $order->billing->zip;
        $email->data["billing_country"] = $order->billing->country;
        $email->data["billing_phone"] = $order->billing->phone;
        $email->data["cardtype"] = $order->cardtype;
        $email->data["accountnumber"] = hideCardNumber($order->accountnumber);
        $email->data["expirationmonth"] = $order->expirationmonth;
        $email->data["expirationyear"] = $order->expirationyear;
        $email->data["billing_address"] = pmpro_formatAddress($order->billing->name,
                                          $order->billing->street,
                                          "", //address 2
                                          $order->billing->city,
                                          $order->billing->state,
                                          $order->billing->zip,
                                          $order->billing->country,
                                          $order->billing->phone);

        if($order->getDiscountCode())
                $email->data["discount_code"] = "<p>" . __("Discount Code", "pmpro") . ": " . $order->discount_code->code . "</p>\n";
        else
                $email->data["discount_code"] = "";

        //send the email
        $email->sendEmail();
}
