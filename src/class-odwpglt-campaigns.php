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
     * Class that implements all around CPT "campaign".
     * @author Ondřej Doněk <ondrejd@gmail.com>
     * @since 0.1.0
     */
    class odwpglt_campaigns {

        /**
         * @var odwpglt_campaigns $instance
         * @since 0.1.0
         */
        protected static $instance;

        /**
         * @return odwpglt_campaigns
         * @since 0.1.0
         */
        protected static function get_instance() {
            if( !( self::$instance instanceof odwpglt_campaigns ) ) {
                self::$instance = new odwpglt_campaigns();
            }
            return self::$instance;
        }

        /**
         * @return void
         * @since 0.1.0
         */
        protected function __construct() {
            //...
        }

        /**
         * Initialize CPT "campaign".
         * @return void
         * @since 0.1.0
         */
        public static function init() {
            $self = self::get_instance();
            $labels = array(
                'name' => __( 'Kampaně', GLT_SLUG ),
                'singular_name' => __( 'Kampaň', GLT_SLUG )
            );
            $args = array(
                'labels' => $labels,
                'public' => false,
                'has_archive' => false,
                'exclude_from_search' => true,
                'show_ui' => true,
                'show_in_menu' => true,
                'show_in_admin_bar' => false,
                'hierarchical' => false,
                'query_var' => false,
                'can_export' => true,
                'menu_position' => 50,
                'menu_icon' => SHWCP_ROOT_URL . '/assets/img/wcp-16.png',
                'supports' => array( 'title', 'author' ),
            );

            register_post_type( GLT_CPT, $args );
        }

        /**
         * Initialize meta boxes for CPT "campaign".
         * @return void
         * @since 0.1.0
         */
        public static function init_boxes() {
            add_action( 'add_meta_boxes', array( 'odwpglt_campaigns', 'add_boxes' ) );
            add_action( 'save_post', array( 'odwpglt_campaigns', 'save_boxes' ), 99, 2 );
        }

        /**
         * @internal Add meta boxes.
         * @return void
         * @since 0.1.0
         */
        public static function add_boxes() {
            add_meta_box( 'odwpglt-box-tender', __( 'Pečovatel', GLT_SLUG ), array( __CLASS__, 'render_box_tender' ), GLT_CPT );
            add_meta_box( 'odwpglt-box-type', __( 'Typ kampaně', GLT_SLUG ), array( __CLASS__, 'render_box_type' ), GLT_CPT );
        }

        /**
         * @internal Renders meta box "Tender".
         * @return void
         * @since 0.1.0
         */
        public static function render_box_tender() {
            wp_nonce_field( 'odwpglt_box_save_action', 'campaign_nonce' );

            $label = __( 'Vyberte pečovatele:', GLT_SLUG );
            $value = '0'; // TODO Get correct value!
            $html = <<<EOC
<p>
    <label for="odwpasp-tender">$label</label>
    <select class="odwpglt-meta-box odwpglt-meta-box-tender" id="odwpglt-tender" name="odwpglt-tender" style="min-width:140px" type="text" value="$value">
        <option value="0">&ndash;&ndash;&ndash;</option>
    </select>
</p>

EOC;

            echo $html;
        }

        /**
         * @internal Renders meta box "Type".
         * @return void
         * @since 0.1.0
         */
        public static function render_box_type() {
            wp_nonce_field( 'odwpglt_box_save_action', 'campaign_nonce' );

            $label = __( 'Vyberte typ kampaně:', GLT_SLUG );
            $value = '0'; // TODO Get correct value!
            $html = <<<EOC
<p>
    <label for="odwpasp-tender">$label</label>
    <select class="odwpglt-meta-box odwpglt-meta-box-tender" id="odwpglt-tender" name="odwpglt-tender" style="min-width:140px" type="text" value="$value">
        <option value="0">&ndash;&ndash;&ndash;</option>
        <option value="1">PPS</option>
        <option value="2">SEO</option>
        <option value="3">Web</option>
    </select>
</p>

EOC;

            echo $html;
        }

        /**
         * Save values of meta boxes.
         * @return void
         * @since 0.1.0
         */
        public static function save_boxes() {
            // Add nonce for security and authentication.
            $nonce_name   = isset( $_POST['odwpglt_campaign_nonce'] ) ? $_POST['odwpglt_campaign_nonce'] : '';
            $nonce_action = 'odwpglt_box_save_action';

            // Check if not an autosave.
            if( wp_is_post_autosave( $post_id ) ) {
                odwpdl_write_log( 'Campaign is not saved - is a autosave!' );
                return;
            }

            // Check if not a revision.
            if( wp_is_post_revision( $post_id ) ) {
                odwpdl_write_log( 'Campaign is not saved - is a revision!' );
                return;
            }

            // Check if nonce is set.
            if( !isset( $nonce_name ) ) {
                odwpdl_write_log( 'Couldn\'t save campaign - NONCE is not set!' );
                return;
            }

            // Check if nonce is valid.
            if( !wp_verify_nonce( $nonce_name, $nonce_action ) ) {
                odwpdl_write_log( 'Couldn\'t save campaign - NONCE not verified!' );
                return;
            }

            // Check if user has permissions to save data.
            if( !current_user_can( 'edit_post', $post_id ) ) {
                odwpdl_write_log( 'Couldn\'t save campaign - user hasn\'t rights!' );
                return;
            }

            //...
        }
    }
endif;

// Initialize CPT

add_action( 'init', array( 'odwpglt_campaigns', 'init' ), 99 );

if( is_admin() ) {
    add_action( 'load-post.php', array( 'odwpglt_campaigns', 'init_boxes' ) );
    add_action( 'load-post-new.php', array( 'odwpglt_campaigns', 'init_boxes' ) );
}

