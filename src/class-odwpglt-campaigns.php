<?php
/**
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/odwp-shwcp_gleton for the canonical source repository
 * @package odwp-shwcp_gleton
 * @since 0.1.0
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'odwpglt_campaigns' ) ) :
    /**
     * Class that implements front-end page "Campaigns".
     * @author Ondřej Doněk <ondrejd@gmail.com>
     * @since 0.1.0
     */
    class odwpglt_campaigns extends main_wcp {

        /**
         * Returns all campaigns for specified lead.
         * @global wpdb $wpdb
         * @return string
         * @since 0.1.0
         */
        public function get_all_campaigns() {
            global $wpdb;

			$table_name = $wpdb->prefix . GLT_CAMPAIGNS/* XXX . $dbnumber */;
            $lead_id = isset( $_GET['lead'] ) ? ( int ) $_GET['lead'] : 0;
            // Note: Calling `main_wcp::load_db_options()` is required 
            // because it initializes db-related class properties.
            $this->load_db_options( $lead_id );
            $lead = $wpdb->get_row( "SELECT * FROM $this->table_main WHERE id = $lead_id" );

            if( !is_object( $lead ) || is_null( $lead ) ) {
                $msg = __( 'Nemohu zobrazit kampaně - <code>lead_id</code> nebylo stanoveno nebo je chybné!', GLT_SLUG );
                $out = <<<EOC
                <p><b>$msg</b></p>

EOC;

                return $out;
            }

            // Lead is found. Display table of its campaigns.
            $page_title = sprintf( __( 'Kampaně pro kontakt <em>%1$s</em>', GLT_SLUG ), $lead->first_name );
            $table_col1_lbl = __( 'Typ', GLT_SLUG );
            $table_col2_lbl = __( 'Stav', GLT_SLUG );
            $table_col3_lbl = __( 'Podstav', GLT_SLUG );
            $table_col4_lbl = __( 'D. d. kontaktu', GLT_SLUG );
            $table_col5_lbl = __( 'Vytvořeno', GLT_SLUG );
            $table_col6_lbl = __( 'Autor', GLT_SLUG );
            $table_col7_lbl = __( 'Dat. zahájení', GLT_SLUG );
            $table_col8_lbl = __( 'Dat. ukončení', GLT_SLUG );
            $table_col9_lbl = __( 'Rychlá úprava', GLT_SLUG );
            $table_col10_lbl = __( 'Pečovatel', GLT_SLUG );

            $out = <<<EOC

                <div class="row">
                    <span class="wcp-button odwpglt-add-campaign odwpglt-campaigns-table-top_add_button">Přidat kampaň</span>
                    <h4 class="odwpglt-campaigns-table-title">$page_title</h4>
                </div>
                <table class="wcp-table odwpglt-campaigns-table">
                    <tbody>
                        <tr id="header-row" class="header-row">
                            <th class="table-head odwpglt-campaign-table-col-type" data-th="$table_col1_lbl">$table_col1_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-tender" data-th="$table_col10_lbl">$table_col10_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-status" data-th="$table_col2_lbl">$table_col2_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-substatus" data-th="$table_col3_lbl">$table_col3_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-n_c_date" data-th="$table_col4_lbl">$table_col4_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-created" data-th="$table_col5_lbl">$table_col5_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-author" data-th="$table_col6_lbl">$table_col6_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-start_date" data-th="$table_col7_lbl">$table_col7_lbl</th>
                            <th class="table-head odwpglt-campaign-table-col-stop_date" data-th="$table_col8_lbl">$table_col8_lbl</th>
                            <th class="edit-header odwpglt-campaign-table-col-edit" data-th="$table_col9_lbl">$table_col9_lbl</th>
                        </tr>

EOC;

            // Get all campaigns for selected lead
            $campaigns = $wpdb->get_results( "
                SELECT a.*,b.first_name 
                FROM $table_name a 
                INNER JOIN wp_shwcp_leads AS b ON b.id = a.lead_id 
                WHERE a.lead_id = $lead_id
            " );

            if( count( $campaigns ) == 0 ) {
                $msg = __( 'Nebyly nalezeny žádné kampaně&hellip;', GLT_SLUG );
                $out .= <<<EOC
                        <tr>
                            <td colspan="6"><p class="odwpglt-no-campaigns-msg"><b>$msg</b></p></td>
                        </tr>

EOC;
            } else {
                $no_tender_lbl = __( 'není', GLT_SLUG );

                foreach( $campaigns as $campaign ) {
                    $status_name = odwpglt_get_status_name( $campaign->status );
                    $status = sprintf( '<span class="odwpglt-campaign-status odwpglt-campaign-status-%1$s">%2$s</span>', $campaign->status, $status_name );
                    $substatus_name = odwpglt_get_substatus_name( $campaign->status, $campaign->substatus );
                    $substatus = sprintf( '<span class="odwpglt-campaign-substatus-%1$s">%2$s</span>', $campaign->substatus, $substatus_name );
                    
                    $tender_id = (int) $campaign->tender;
                    $tender = __( '---', GLT_SLUG );

                    if( empty( $tender_id ) ) {
                        $tender = '<span class="odwpglt-campaign-no-tender">' . $no_tender_lbl . '</span>';
                    } else {
                        $tender_obj = get_user_by( 'id', $tender_id );
                        if( ( $tender_obj instanceof WP_User ) ) {
                            $tender = sprintf( '<a href="%1$s">%2$s</a>', '#', $tender_obj->user_nicename );
                        } 
                    }

                    $n_c_date = ( '0000-00-00' == $campaign->n_c_date || empty( $campaign->n_c_date ) )
                            ? __( '---', GLT_SLUG )
                            : date( 'j.n.Y', strtotime( $campaign->n_c_date ) );
                    $created = date( 'j.n.Y H:i', strtotime( $campaign->created ) );

                    $author_obj = get_user_by( 'id', $campaign->author_id );
                    $author = __( '---', GLT_SLUG );
                    if( ( $author_obj instanceof WP_User ) ) {
                        $author = sprintf( '<a href="%1$s">%2$s</a>', '#', $author_obj->user_nicename );
                    }

                    $start_date = ( '0000-00-00' == $campaign->start_date || empty( $campaign->start_date ) )
                            ? __( '---', GLT_SLUG )
                            : date( 'j.n.Y', strtotime( $campaign->start_date ) );
                    $stop_date = ( '0000-00-00' == $campaign->stop_date || empty( $campaign->stop_date ) )
                            ? __( '---', GLT_SLUG )
                            : date( 'j.n.Y', strtotime( $campaign->stop_date ) );
                    $del_campaign_msg = __( 'Smazat kampaň', GLT_SLUG );

                    $out .= <<<EOC
                        <tr class="odwpglt-campaign-table-row" id="odwpglt-campaign-table-row-{$campaign->id}">
                            <td class="odwpglt-campaign-table-cell-type" data-th="$table_col1_lbl">{$campaign->type}</td>
                            <td class="odwpglt-campaign-table-cell-tender" data-th="$table_col10_lbl">{$tender}</td>
                            <td class="odwpglt-campaign-table-cell-status" data-th="$table_col2_lbl">$status</td>
                            <td class="odwpglt-campaign-table-cell-substatus" data-th="$table_col3_lbl">$substatus</td>
                            <td class="odwpglt-campaign-table-cell-date" data-th="$table_col4_lbl">$n_c_date</td>
                            <td class="odwpglt-campaign-table-cell-created" data-th="$table_col5_lbl">$created</td>
                            <td class="odwpglt-campaign-table-cell-author" data-th="$table_col6_lbl">$author</td>
                            <td class="odwpglt-campaign-table-cell-start_date" data-th="$table_col7_lbl">$start_date</td>
                            <td class="odwpglt-campaign-table-cell-stop_date" data-th="$table_col8_lbl">$stop_date</td>
                            <td class="odwpglt-campaign-table-cell-edit" data-th="$table_col9_lbl">
                                <span class="odwpglt-delete-campaign" data-campaign_id="{$campaign->id}" title="$del_campaign_msg">
                                    <i class="wcp-red wcp-sm md-remove-circle-outline"></i>
                                </span>
                                <span class="odwpglt-delete-all-check" data-campaign_id="{$campaign->id}">
                                    <input class="odwpglt-delete-all-checkbox" id="odwpglt-delete-all-checkbox-{$campaign->id}" type="checkbox">
                                    <label for="odwpglt-delete-all-checkbox-{$campaign->id}"></label>
                                </span>
                            </td>
                        </tr>

EOC;
                }
            }

            $out .= <<<EOC
                    </tbody>
                </table>

EOC;

            return $out;
        }
    }
endif;
