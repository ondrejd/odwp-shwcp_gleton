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

            $this->lead_id = isset( $_GET['lead'] ) ? ( int ) $_GET['lead'] : 0;
            $this->load_db_options( $this->lead_id );

            $this->lead = $wpdb->get_row (
                "
                    SELECT l.*
                    FROM $this->table_main l
                    WHERE l.id = {$this->lead_id};
                "
            );

            

            if( !is_object( $this->lead ) || is_null( $this->lead ) ) {
                $msg = __( 'Nemohu zobrazit kampaně - <code>lead_id</code> nebylo stanoveno nebo je chybné!', GLT_SLUG );
                $out = <<<EOC
                <p><b>$msg</b></p>

EOC;
            } else {
                $out = <<<EOC
                    <p> ... <b>{$this->lead->first_name}</b> ... </p>

EOC;
            }

            return $out;
        }
    }
endif;
