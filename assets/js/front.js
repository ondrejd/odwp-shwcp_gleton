/**
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/odwp-shwcp_gleton for the canonical source repository
 * @package odwp-shwcp_gleton
 * @since 0.0.1
 */
/**
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/odwp-shwcp_gleton for the canonical source repository
 * @package odwp-shwcp_gleton
 * @since 0.1.0
 */

jQuery(document).ready(function() {

    console.log( odwpglt );

    /**
     * Individual front page - editable web input
     */

    var change_url = false,
        change_url_last = null;
    
    // Start editing - show input and hide link
    jQuery( "#odwpglt-change_url_individual-icon" ).click(function(e) {
        if( change_url === true ) {
            return;
        }

        var input = jQuery( "#odwpglt-change_url_individual-input" );
        change_url = true;
        change_url_last = input.val();
        jQuery( "#odwpglt-change_url_individual-link" ).css("display", "none");
        input.css("display", "block");
        jQuery( "#odwpglt-change_url_individual-cancel" ).css("display", "inline-block");
        jQuery( "#odwpglt-change_url_individual-submit" ).css("display", "inline-block");
    });
    // Cancel edit - restore value, hide input and show link
    jQuery( "#odwpglt-change_url_individual-cancel" ).click(function(e) {
        if( change_url !== true ) {
            return;
        }

        jQuery( "#odwpglt-change_url_individual-input" ).css("display", "none").val(change_url_last);
        jQuery( "#odwpglt-change_url_individual-cancel" ).css("display", "none");
        jQuery( "#odwpglt-change_url_individual-submit" ).css("display", "none");
        jQuery( "#odwpglt-change_url_individual-link" ).css("display", "inline-block");
        change_url = false;
        change_url_last = null;
    });
    // Submit edit - save value, hide input and show link
    jQuery( "#odwpglt-change_url_individual-submit" ).click(function(e) {
        console.error("Not finished yet!");
        //...
    });

    /**
     * Individual front page - campaigns
     */

    // On status select change
    jQuery( ".odwpglt-campaign-status" ).change(function(e) {
        var status_select = jQuery( this );
        var substatus_select = jQuery( "#" + status_select.attr("id").replace("--status", "--substatus") );
        var value = status_select.val();

        status_select.attr("data-status", value);

        if (value == "1") {
            substatus_select.find( "option:not(:first)" ).remove();
            substatus_select.prop("disabled", true);
            return;
        }

        var status = odwpglt.statuses[status_select.val()];
        if (status) {
            substatus_select.find( "option:not(:first)" ).remove();

            var substatuses = odwpglt.statuses[status_select.val()].items;
            for (var substatus in substatuses) {
                substatus_select.append(new Option(substatuses[substatus], substatus));
                substatus_select.prop("disabled", false);
            }
        }
    });

    // TODO Při změně stavu (a substavu) se také musí zobrazit/skrýt poznámka a datum dalšího kontaktu!

    // On substatus select change
    jQuery( ".odwpglt-campaign-substatus" ).change(function(e) {
        var substatus_select = jQuery( this );
        substatus_select.attr("data-substatus", substatus_select.val());
    });

    // On campaign checkbox change
    jQuery( ".odwpglt-campaign--checkbox" ).change(function(e) {
        console.log(e);
        var cls_name = jQuery( this ).attr("id").replace("--checkbox", "");
        console.log(cls_name);

        if (jQuery( this ).prop("checked") === true) {
            jQuery( "." + cls_name ).show();
        } else {
            jQuery( "." + cls_name ).hide();
        }
    });

    /**
     * Page with campaigns
     */

    // Create new campaign
    jQuery( "span.odwpglt-add-campaign" ).click(function(e) {
        console.log( e );
        console.log( "TODO Add campaign..." );

        var modalBody = "";

        /*var leadID = 'new';
        $.post(WCP_Ajax.ajaxurl, {
            // wp ajax action
            action: 'ajax-wcpfrontend',
            // vars
            lead_id: leadID,
            new_lead: 'true',
            nextNonce : WCP_Ajax.nextNonce,
            postID : WCP_Ajax.postID
        
        }, function(response) {
            if (response.logged_in == 'false') {
                showLogInDiag(response.title, response.body, response.login_button, response.close);
                return false;
            }
            showLeadDiag(response, leadID);
        });*/
/*
- id	        bigint(20) Auto Increment	 
- lead_id	    bigint(20)	 
+ tender	    bigint(20) [0]	 
+ type	        varchar(55)	 
- status	    smallint(1) [1]	 
- substatus	    smallint(1) [0]	 
- note	    longtext NULL	 
- n_c_date	date NULL	 
- created	    datetime [current_timestamp()]	 
- author_id	bigint(20)	 
+ start_date	date NULL	 
+ stop_date	date NULL
*/
        modalBody += '<div class="wcp-add-lead odwpglt-add-campaign row">'
                   +   '<div class="col-md-12">'
                   +     '<p style="color:#f30;font-weight:bold">XXX Finish this!</p>'
                   +   '</div>'
                   + '</div>';

        // add large class to modal (remove for smaller ones)
        jQuery( ".wcp-modal" ).find(".modal-dialog").addClass("modal-lg");
        jQuery( ".wcp-modal" ).find(".modal-title").html(odwpglt.addCampaign);
        jQuery( ".wcp-modal" ).find(".modal-body").html(modalBody);
        var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">'
                    + odwpglt.cancelButton + '</button>'
                    + '<button type="button" class="btn btn-primary confirm-add-campaign">' 
                    + odwpglt.addCampaignButton + '</button>';
        jQuery(".wcp-modal").find(".modal-footer").html(footer);
        jQuery(".wcp-modal").modal();
    });

    // Delete campaign
    jQuery( ".odwpglt-delete-campaign" ).click(function(e) {
        var campaign_id = jQuery( this ).data( "campaign_id" );
        var modalBody = "";
        var headers = [];
        var fields = [];

        console.log( "TODO Delete campaign...", campaign_id );

        jQuery( ".header-row" ).find("th").each(function() {
            if(!jQuery( this ).hasClass("edit-header")) {
                headers.push( jQuery( this ).text());
            }
        });

        jQuery( ".odwpglt-campaigns-table" ).find("#odwpglt-campaign-table-row-" + campaign_id).find("td").each(function() {
            if(!jQuery( this ).hasClass("odwpglt-campaign-table-cell-edit")) {
                fields.push(jQuery(this).text());
            }
        });

        console.log(headers, fields);

        modalBody += '<div class="wcp-edit-lead odwpglt-edit-campaign row">';

        var i = 0, total = headers.length;
        jQuery( headers ).each(function(k, v) {
            modalBody += '<div class="col-md-6">'
                       + '<label for="del-' + k + '">' + v + '</label>'
                       + '<span class="lead_field del-' + k + '">' + fields[i] + '</span>'
                       + '</div>';
            i++;
        });

        modalBody += '</div>';

        // add large class to modal (remove for smaller ones)
        jQuery( ".wcp-modal" ).find(".modal-dialog").addClass("modal-lg");
        jQuery( ".wcp-modal" ).find(".modal-title").html(odwpglt.confirmCampaignRemoval);
        jQuery( ".wcp-modal" ).find(".modal-body").html(modalBody);
        var footer = '<button type="button" class="btn btn-default" data-dismiss="modal">'
                    + odwpglt.cancelButton + '</button>'
                    + '<button type="button" class="btn btn-primary confirm-delete-campaign odwpglt-campaign-' + campaign_id + '">' 
                    + odwpglt.removeCampaignButton + '</button>';
        jQuery(".wcp-modal").find(".modal-footer").html(footer);
        jQuery(".wcp-modal").modal();
    });
});
