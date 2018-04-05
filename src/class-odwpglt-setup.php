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

if( !class_exists( 'odwpglt_setup' ) ) :
    /**
     * Setup class for our plugin.
     * @see setup_wcp
     * @since 0.1.0
     */
    class odwpglt_setup extends main_wcp {
        // properties

        // methods

        /**
         * Check if our table exists upon activation.
         * @global wpdb $wpdb
         * @return boolean
         * @since 0.1.0
         */
        public function table_exists() {
            global $wpdb;
            $table_name = $wpdb->prefix . GLT_CAMPAIGNS;
            return ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) === $table_name );
        }

		/**
		 * Drop our database table.
         * @global wpdb $wpdb
         * @param string $dbnumber
         * @return void
         * @since 0.1.0
		 */
		public function drop_tables( $dbnumber = '' ) {
			global $wpdb;
			$table_name = $wpdb->prefix . GLT_CAMPAIGNS . $dbnumber;
			$wpdb->query( "DROP TABLE IF EXISTS $table_main" );
        }
        
        /**
         * Return backup SQL.
         * @global wpdb $wpdb
         * @param array $table
         * @return void
         * @since 0.1.0
         * @todo This is not implemented yet - will be if customer will want it.
         */
        public function backup_tables( $tables ) {
            global $wpdb;
            //...
        }

        /**
         * Create database table with optional table number increment for extra databases.
         * @global wpdb $wpdb
         * @param string $dbnumber
         * @return void
         * @since 0.1.0
         */
        public function install( $dbnumber = '' ) {
            global $wpdb;

            $collate = $wpdb->get_charset_collate();
			$table_name = $wpdb->prefix . GLT_CAMPAIGNS . $dbnumber;
            $table_sql = <<<EOC
CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    tender bigint(20) DEFAULT 0 NOT NULL,
    type varchar(55) NOT NULL,
    status smallint(1) DEFAULT 0 NOT NULL,
    substatus smallint(1) DEFAULT 0 NOT NULL,
    note longtext,
    n_c_date date DEFAULT '0000-00-00' NOT NULL,
    UNIQUE KEY id (id)
) $collate;
EOC;

            require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
            dbDelta( $table_sql );
            add_option( 'odwpglt_table_v', GLT_VERSION );
        }

        /**
         * Install initial data.
         * @global wpdb $wpdb
         * @param string $dbnumber
         * @return void
         * @since 0.1.0
         */
        public function install_data( $dbnumber = '' ) {
            global $wpdb;
            //...
        }

        /**
         * Install options of the plugin.
         * @param string $dbnumber
         * @return void
         * @since 0.1.0
         */
        public function install_options( $dbnumber = '' ) {
            //...
        }
    }
endif;
