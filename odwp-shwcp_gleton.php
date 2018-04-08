<?php
/**
 * Plugin Name: Customizations for WP Contacts plugin
 * Plugin URI: https://github.com/ondrejd/odwp-shwcp_gleton
 * Description: Plugin that changes behavior of <a href="http://wpcontacts.co/" target="blank">WP Contacts</a> plugin in way which one of my clients wanted.
 * Version: 0.3.0
 * Author: Ondřej Doněk
 * Author URI: https://ondrejd.com/
 * License: GPLv3
 * Requires at least: 4.8
 * Tested up to: 4.8.4
 * Tags: contacts,management
 * Donate link: https://www.paypal.me/ondrejd
 *
 * Text Domain: odwpglt
 * Domain Path: /languages/
 *
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/odwp-shwcp_gleton for the canonical source repository
 * @package odwp-shwcp_gleton
 * @since 0.0.1
 */

/**
 * This file is just a bootstrap. It checks if requirements of plugins
 * are met and accordingly either allow activating the plugin or stops
 * the activation process.
 *
 * Requirements can be specified either for PHP interperter or for
 * the WordPress self. In both cases you can specify minimal required
 * version and required extensions/plugins.
 *
 * If you are using copy of original file in your plugin you should change
 * prefix "odwpglt" and name "odwp-shwcp_gleton" to your own values.
 *
 * To set the requirements go down to line 200 and define array that
 * is used as a parameter for `odwpglt_check_requirements` function.
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
}

// Some constants
defined( 'GLT_SLUG' ) || define( 'GLT_SLUG', 'odwpglt' );
defined( 'GLT_NAME' ) || define( 'GLT_NAME', 'odwp-shwcp_gleton' );
defined( 'GLT_PATH' ) || define( 'GLT_PATH', dirname( __FILE__ ) . '/' );
defined( 'GLT_FILE' ) || define( 'GLT_FILE', __FILE__ );
defined( 'GLT_VERSION' ) || define( 'GLT_VERSION', '0.3.0' );
defined( 'GLT_TEMPLATE' ) || define( 'GLT_TEMPLATE', 'odwpglt-front-template.php' );
defined( 'GLT_CAMPAIGNS' ) || define( 'GLT_CAMPAIGNS', 'odwpglt_campaigns' );


if( !function_exists( 'odwpglt_check_requirements' ) ) :
    /**
     * Checks requirements of our plugin.
     * @global string $wp_version
     * @param array $requirements
     * @return array
     * @since 0.0.1
     */
    function odwpglt_check_requirements( array $requirements ) {
        global $wp_version;

        // Initialize locales
        load_plugin_textdomain( GLT_SLUG, false, GLT_NAME . '/languages' );

        /**
         * @var array Hold requirement errors
         */
        $errors = [];

        // Check PHP version
        if( ! empty( $requirements['php']['version'] ) ) {
            if( version_compare( phpversion(), $requirements['php']['version'], '<' ) ) {
                $errors[] = sprintf(
                        __( 'Running PHP is lower version that this plugin requires (at least <b>%1$s</b> is required)!', GLT_SLUG ),
                        $requirements['php']['version']
                );
            }
        }

        // Check PHP extensions
        if( count( $requirements['php']['extensions'] ) > 0 ) {
            foreach( $requirements['php']['extensions'] as $req_ext ) {
                if( ! extension_loaded( $req_ext ) ) {
                    $errors[] = sprintf(
                            __( 'This plugin requires PHP extension <b>%1$s</b> but the extension is not installed!', GLT_SLUG ),
                            $req_ext
                    );
                }
            }
        }

        // Check WP version
        if( ! empty( $requirements['wp']['version'] ) ) {
            if( version_compare( $wp_version, $requirements['wp']['version'], '<' ) ) {
                $errors[] = sprintf(
                        __( 'This plugin requires higher version of <b>WordPress</b> (at least <b>%1$s</b> is required)!', GLT_SLUG ),
                        $requirements['wp']['version']
                );
            }
        }

        // Check WP plugins
        if( count( $requirements['wp']['plugins'] ) > 0 ) {
            $active_plugins = (array) get_option( 'active_plugins', [] );

            foreach( $requirements['wp']['plugins'] as $req_plugin ) {
                if( ! in_array( $req_plugin, $active_plugins ) ) {
                    $errors[] = sprintf(
                            __( 'Plugin <b>%1$s</b> is required but not installed!', GLT_SLUG ),
                            $req_plugin
                    );
                }
            }
        }

        return $errors;
    }
endif;


if( !function_exists( 'odwpglt_deactivate_raw' ) ) :
    /**
     * Deactivate plugin by the raw way (it updates directly WP options).
     * @return void
     * @since 0.0.1
     */
    function odwpglt_deactivate_raw() {
        $active_plugins = get_option( 'active_plugins' );
        $out = [];

        foreach( $active_plugins as $key => $val ) {
            if( $val != GLT_NAME . '/' . GLT_NAME . '.php' ) {
                $out[$key] = $val;
            }
        }

        update_option( 'active_plugins', $out );
    }
endif;


if( !function_exists( 'readonly' ) ) :
    /**
     * Prints HTML readonly attribute. It's an addition to WP original
     * functions {@see disabled()} and {@see checked()}.
     * @param mixed $value
     * @param mixed $current (Optional.) Defaultly TRUE.
     * @return string
     * @since 0.0.1
     */
    function readonly( $current, $value = true ) {
        if( $current == $value ) {
            echo ' readonly';
        }
    }
endif;


/**
 * Errors from the requirements check
 * @var array
 * @since 0.0.1
 */
$odwpglt_errs = odwpglt_check_requirements( [
    'php' => [
        // Enter minimum PHP version you needs
        'version' => '5.4',
        // Enter extensions that your plugin needs
        'extensions' => [
            //'gd',
        ],
    ],
    'wp' => [
        // Enter minimum WP version you need
        'version' => '4.8',
        // Enter WP plugins that your plugin needs
        'plugins' => [
            'shwcp/sh-wcp.php',
        ],
    ],
] );

// Check if requirements are met or not
if( count( $odwpglt_errs ) > 0 ) {
    // Requirements are not met
    odwpglt_deactivate_raw();

    // In administration print errors
    if( is_admin() ) {
        add_action( 'admin_notices', function() use ( $odwpglt_errs ) {
            $err_head = __( '<b>Customizations for WP Contacts plugin</b>: ', GLT_SLUG );

            foreach( $odwpglt_errs as $err ) {
                printf( '<div class="error"><p>%1$s</p></div>', $err_head . $err );
            }
        } );
    }
} else {
    // Requirements are met so initialize the plugin...
    // FIXED CPT změnit na normální tabulku v databází (tak jako je to v samotném SHWCP).
    // FIXED Přidat na front-end admina do seznamu kontaktů ikonku s odkazem na seznam kampaní.
    // FIXED Zobrazit tabulku s kampaněmi pro vybraný kontakt.
    // TODO Dokončit popup pro přidání/editaci nové kampaně.
    // TODO Přidat naše Ajax akce (přidání, editace, smazání kampaně adminem).
    // TODO Zobrazení pro obchodní manažery (jen karty dle kampaní).

	// Translations
    if( !function_exists( 'odwpglt_load_textdomain' ) ) :
        /**
         * Load translations.
         * @return void
         * @since 0.1.0
         */
        function odwpglt_load_textdomain() {
            $path = basename( dirname( __FILE__ ) ) . '/languages';
            load_plugin_textdomain( GLT_SLUG, false, $path );
        }
    endif;
    add_action( 'init', 'odwpglt_load_textdomain' );

    if( !function_exists( 'odwpglt_load_plugin_last' ) ) :
        /**
         * Ensure that our plugin is loaded as the last one.
         * @return void
         * @since 0.0.1
         */
        function odwpglt_load_plugin_last()
        {
            $path = str_replace( WP_PLUGIN_DIR . '/', '', __FILE__ );
            if ( $plugins = get_option( 'active_plugins' ) ) {
                if ( $key = array_search( $path, $plugins ) ) {
                    array_splice( $plugins, $key, 1 );
                    array_push( $plugins, $path );
                    update_option( 'active_plugins', $plugins );
                }
            }
        }
    endif;
    add_action( 'activated_plugin', 'odwpglt_load_plugin_last' );

    if( !function_exists( 'odwpglt_setup' ) ) :
        /**
         * Set up the default tables on plugin activation.
         * @global wpdb $wpdb
         * @return void
         * @since 0.1.0
         */
        function odwpglt_setup( $network_wide ) {
            odwpdl_write_log( '$network_wide="'.$network_wide.'"' );
            include GLT_PATH . 'src/class-odwpglt-setup.php';

            if( is_multisite() && $network_wide ) {
                // multisite installation
                global $wpdb;
                foreach( $wpdb->get_col( "" ) as $blog_id ) {
                    switch_to_blog( $blog_id );
                    $odwpglt_setup = new odwpglt_setup();
                    if( !$odwpglt_setup->table_exists() ) {
                        $odwpglt_setup->install();
                        $odwpglt_setup->install_data();
                        $odwpglt_setup->install_options();
                    }
                }
            } else {
                // standard (no multisite) installation
                $odwpglt_setup = new odwpglt_setup();
                if( !$odwpglt_setup->table_exists() ) {
                    $odwpglt_setup->install();
                    $odwpglt_setup->install_data();
                    $odwpglt_setup->install_options();
                }
            }
        }
    endif;
    register_activation_hook( __FILE__, 'odwpglt_setup');

    // Include all what is required
    include GLT_PATH . 'defines.php';
    include GLT_PATH . 'src/class-odwpglt-page_templater.php';
    include GLT_PATH . 'src/class-odwpglt-front.php';

    // And initialize it
    $odwpglt_front = new odwpglt_front();
    $odwpglt_front->front_init();

	add_action( 'init', array( $odwpglt_front, 'get_the_current_user' ) );
}
