/*
        Note that the PMPro Generic Payment Gateway plugin only loads this JS on the edit membership level page.
*/
function toggle_pgp_level_settings_fields() {
    "use strict";

    if (jQuery('#pgp_setting').val() > 0 ) {
        jQuery('tr.pgp_level_settings_field').show();
    } else {
        jQuery('tr.pgp_level_settings_field').hide();
    }
}

jQuery(document).ready(function () {
        "use strict";
        
        toggle_pgp_level_settings_fields();

        //hide/show recurring fields when pgp or recurring settings change
        jQuery('#pgp_setting').change(function () {
                toggle_pgp_level_settings_fields();
        });
});
