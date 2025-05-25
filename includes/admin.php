<?php

/*
        Add settings to the edit levels page
        *** Warning!!! Changed _check_ for _generic_ - NOT suere if that is correct!!!
*/
//show the checkbox on the edit level page
function pmprogpg_pmpro_membership_level_after_other_settings()
{
        $level_id = intval($_REQUEST['edit']);
        $options = pmprogpg_getOptions($level_id);
        $generic_gateway_label = get_option( 'pmpro_generic_gateway_label' ) ?: __( 'Generic', 'pmpro-generic-gateway' ); // Default to 'Pay with Generic' if no option is set.
?>
<h3 class="topborder"><?php  echo esc_html( sprintf( __( 'Pay with %s Settings', 'pmpro-generic-gateway' ), $generic_gateway_label ) ); ?></h3>
<p><?php echo esc_html( sprintf( __( 'Change this setting to allow or disallow the "Pay with %s" option for this level.', 'pmpro-generic-gateway' ), $generic_gateway_label ) ); ?></p>
<table>
  <tbody class="form-table">
    <tr>
      <th scope="row" valign="top"><label for="pgp_setting"><?php echo esc_html( sprintf( __( 'Allow Paying with %s:', 'pmpro-generic-gateway' ), $generic_gateway_label ) );?></label></th>
      <td>
        <select id="pgp_setting" name="pgp_setting">
          <option value="0" <?php selected($options['setting'], 0);?>><?php esc_html_e( 'No. Use the default gateway only.', 'pmpro-generic-gateway' );?></option>
          <option value="1" <?php selected($options['setting'], 1);?>><?php echo esc_html( sprintf( __( 'Yes. Users choose between default gateway and %s.', 'pmpro-generic-gateway' ), $generic_gateway_label ) );?></option>
          <option value="2" <?php selected($options['setting'], 2);?>><?php echo esc_html( sprintf( __( 'Yes. Users can only pay with %s.', 'pmpro-generic-gateway' ), $generic_gateway_label ) );?></option>
        </select>
      </td>
    </tr>
    <tr class="pgp_level_settings_field">
      <th scope="row" valign="top"><label for="pgp_renewal_days"><?php _e('Send Renewal Emails:', 'pmpro-generic-gateway');?></label></th>
        <td>
          <input type="text" id="pgp_renewal_days" name="pgp_renewal_days" size="5" value="<?php echo esc_attr($options['renewal_days']);?>" /> <?php _e('days before renewal.', 'pmpro-generic-gateway');?>
        </td>
    </tr>
    <tr class="pgp_level_settings_field">
      <th scope="row" valign="top"><label for="pgp_reminder_days"><?php _e('Send Reminder Emails:', 'pmpro-generic-gateway');?></label></th>
      <td>
        <input type="text" id="pgp_reminder_days" name="pgp_reminder_days" size="5" value="<?php echo esc_attr($options['reminder_days']);?>" /> <?php _e('days after a missed payment.', 'pmpro-generic-gateway');?>
      </td>
    </tr>
    <tr class="pgp_level_settings_field">
      <th scope="row" valign="top"><label for="pgp_cancel_days"><?php _e('Cancel Membership:', 'pmpro-generic-gateway');?></label></th>
      <td>
        <input type="text" id="pgp_cancel_days" name="pgp_cancel_days" size="5" value="<?php echo esc_attr($options['cancel_days']);?>" /> <?php _e('days after a missed payment.', 'pmpro-generic-gateway');?>
      </td>
    </tr>
  </tbody>
</table>
<?php
}
add_action('pmpro_membership_level_after_other_settings', 'pmprogpg_pmpro_membership_level_after_other_settings');

//save generic payment gateway settings when the level is saved/added
function pmprogpg_pmpro_save_membership_level($level_id)
{
        //get values
        if(isset($_REQUEST['pgp_setting']))
                $pgp_setting = intval($_REQUEST['pgp_setting']);
        else
                $pgp_setting = 0;

        $renewal_days = intval($_REQUEST['pgp_renewal_days']);
        $reminder_days = intval($_REQUEST['pgp_reminder_days']);
        $cancel_days = intval($_REQUEST['pgp_cancel_days']);

        //build array
        $options = array(
                'setting' => $pgp_setting,
                'renewal_days' => $renewal_days,
                'reminder_days' => $reminder_days,
                'cancel_days' => $cancel_days,
        );

        //save
        delete_option('pmpro_generic_gateway_setting_' . $level_id);
        delete_option('pmpro_generic_gateway_options_' . $level_id);
        add_option('pmpro_generic_gateway_options_' . intval($level_id), $options, "", "no");
}
add_action("pmpro_save_membership_level", "pmprogpg_pmpro_save_membership_level");

/**
 * Enqueue scripts in the dashboard.
 */
function pmprogpg_admin_enqueue_scripts() {
        //make sure this is the edit level page
        
        wp_register_script('pmprogpg-admin', plugins_url( 'js/pmpro-generic-gateway-admin.js', PMPRO_PAY_BY_CHECK_BASE_FILE ), array( 'jquery' ), PMPROPBC_VER );
        wp_enqueue_script('pmprogpg-admin');
}
add_action('admin_enqueue_scripts', 'pmprogpg_admin_enqueue_scripts' );
