/**
 * Slightly changed original version of WPC script.
 */
jQuery(function ($) {  // use $ for jQuery
    "use strict";
console.log( "odwp-shwcp_gleton/assets/js/admin-meta.js" );
	/* Meta Box hide / show */
    $(document).ready(function() {
        if ($('#page_template').length) {
            var selected = $('#page_template option:selected').val();
            console.log(selected);
			if (selected == 'wcp-fullpage-template.php' || selected == 'odwpglt-front-template.php') {
				$(document).find('#wcp_db_options').show();
			} else {
				$(document).find('#wcp_db_options').hide();
			}
        }
    });

	/* On change Meta Box hide / show */
	$(document).on('change', '#page_template', function() {
		var selected = $('#page_template option:selected').val();
        console.log(selected);
        if (selected == 'wcp-fullpage-template.php' || selected == 'odwpglt-front-template.php') {
            $(document).find('#wcp_db_options').show();
        } else {
            $(document).find('#wcp_db_options').hide();
        }
	});

});
