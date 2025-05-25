<?php

/**
 * Show levels with pending payments on the account page.
 */
function pmprogpg_pmpro_account_bullets_bottom() {
        // Get all pending generic orders for this user.
        $order_query_args = array(
                'user_id' => get_current_user_id(),
                'status' => 'pending',
                'gateway' => 'generic'
        );
        $orders = MemberOrder::get_orders( $order_query_args );

        foreach ( $orders as $order ) {
                ?>
                <li>
                        <?php
                        // Get the level.
                        $level = pmpro_getLevel( $order->membership_id );

                        // Check if the user is pending for the level.
                        if ( ! pmpro_hasMembershipLevel( $order->membership_id, $order->user_id ) ) {
                                printf( esc_html__('%sYour %s membership is pending.%s We are still waiting for payment for %syour latest invoice%s.', 'pmpro-generic-gateway'), '<strong>', esc_html( $level->name ), '</strong>', sprintf( '<a href="%s">', pmpro_url('invoice', '?invoice=' . $order->code) ), '</a>' );
                        } else {
                                printf( esc_html__('%sImportant Notice:%s We are still waiting for payment on %sthe latest invoice%s for your %s membership.', 'pmpro-generic-gateway'), '<strong>', '</strong>', sprintf( '<a href="%s">', pmpro_url('invoice', '?invoice=' . $order->code ) ), '</a>', esc_html( $level->name ) );
                        }
                        ?>
                </li>
                <?php
        }
}
add_action('pmpro_account_bullets_bottom', 'pmprogpg_pmpro_account_bullets_bottom');

/**
 * If an invoice is pending, show a message on the invoice page.
 */
function pmprogpg_pmpro_invoice_bullets_bottom() {
        if ( empty( $_REQUEST['invoice'] ) ) {
                return;
        }

        // Get the order.
        $order = new MemberOrder( $_REQUEST['invoice'] );

        // Check if it is pending and a check payment.
        if ( $order->status == 'pending' && $order->gateway == 'generic' ) {
                ?>
                <li>
                        <?php
                        // Check if the user is pending for the level.
                        if ( ! pmpro_hasMembershipLevel( $order->membership_id, $order->user_id ) ) {
                                printf( esc_html__('%sMembership pending.%s We are still waiting for payment of this invoice.', 'pmpro-generic-gateway'), '<strong>', '</strong>' );
                        } else {
                                printf( esc_html__('%sImportant Notice:%s We are still waiting for payment of this invoice.', 'pmpro-generic-gateway'), '<strong>', '</strong>' );
                        }
                        ?>
                </li>
                <?php
        }
}
add_action('pmpro_invoice_bullets_bottom', 'pmprogpg_pmpro_invoice_bullets_bottom');

/**
 * Filter the confirmation message of Paid Memberships Pro when the gateway is check and the payment isn't successful.
 *
 * @param string $confirmation_message The confirmation message before it is altered.
 * @param object $invoice The PMPro MemberOrder object.
 * @return string $confirmation_message The level confirmation message.
 */
function pmprogpg_confirmation_message( $confirmation_message, $invoice ) {

        // Only filter orders that are done by check.
        if ( $invoice->gateway !== 'generic' || ( $invoice->gateway == 'generic' && $invoice->status == 'success' ) ) {
                return $confirmation_message;
        }

        $user = get_user_by( 'ID', $invoice->user_id );
        
        $confirmation_message = '<p>' . sprintf( __( 'Thank you for your membership to %1$s. Your %2$s membership status is: <b>%3$s</b>.', 'pmpro-generic-gateway' ), get_bloginfo( 'name' ), $invoice->membership_level->name, $invoice->status ) . ' ' . __( 'Once payment is received and processed you will gain access to your membership content.', 'pmpro-generic-gateway' ) . '</p>';

        // Put the level confirmation from level settings into the message.
        $level_obj = pmpro_getLevel( $invoice->membership_id );
        if ( ! empty( $level_obj->confirmation ) ) {
                $confirmation_message .= wpautop( wp_unslash( $level_obj->confirmation ) );
        }

        $confirmation_message .= '<p>' . sprintf( __( 'Below are details about your membership account and a receipt for your initial membership invoice. A welcome email with a copy of your initial membership invoice has been sent to %s.', 'pmpro-generic-gateway' ), $user->user_email ) . '</p>';

        // Put the generic instructions into the message.
        $invoice->getMembershipLevel();
        if ( ! empty( $invoice ) && $invoice->gateway == 'generic' && ! pmpro_isLevelFree( $invoice->membership_level ) ) {
                $confirmation_message .= '<div class="pmpro_payment_instructions">' . wpautop( wp_unslash( get_option( 'pmpro_instructions' ) ) ) . '</div>';
        }

        // Run it through wp_kses_post in case someone translates the strings to have weird code.
        return wp_kses_post( $confirmation_message );

}
add_filter( 'pmpro_confirmation_message', 'pmprogpg_confirmation_message', 10, 2 );
