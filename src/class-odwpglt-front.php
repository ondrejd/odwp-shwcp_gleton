<?php
/**
 * @author Ondřej Doněk <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/odwp-shwcp_gleton for the canonical source repository
 * @package odwp-shwcp_gleton
 * @since 0.0.1
 */

if( !defined( 'ABSPATH' ) ) {
    exit;
}

if( !class_exists( 'wcp_front' ) ) {
    include plugin_dir_path( dirname( GLT_FILE ) ) . 'shwcp/sh-wcp.php';
}

if( !class_exists( 'odwpglt_front' ) ) :
    /**
     * Class for frontend display, extends WP Contacts {@see wcp_front} class.
     * @since 0.0.1
     */
    class odwpglt_front extends wcp_front {

        // Properties

        protected $posts_templates;

        // methods

        /**
         * Initialize our front page class.
         * @return void
         * @see wcp_front::front_init()
         * @since 0.0.1
         */
        public function front_init() {
            parent::front_init();

            // Load our front page template
            add_action( 'plugins_loaded', array( $this, 'init_template' ) );

            // Fix original JavaScript for metaboxes
            if( is_admin() ) {
                add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 100, 1 );
            }
        }

        /**
         * Initialize our front page templage.
         * @return void
         * @see SHWCPPageTemplater::__construct()
         * @since 0.0.1
         */
        public function init_template() {

            if ( version_compare( floatval($GLOBALS['wp_version']), '4.7', '<' ) ) { // 4.6 and older
                add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'register_project_templates' ) );
            } else {
                add_filter( 'theme_page_templates', array( $this, 'add_new_template' ) );
            }

            add_filter( 'page_attributes_dropdown_pages_args', array( $this, 'register_project_templates' ) );
            add_filter( 'wp_insert_post_data',  array( $this, 'register_project_templates' ) );
            add_filter( 'template_include', array( $this, 'view_project_template') );

            $this->posts_templates = array(
                GLT_TEMPLATE => __( 'Upravená šablona pro WP Kontakty', GLT_SLUG ),
            );
        }

        /**
         * @internal Register our posts templates.
         * @param array $posts_templates
         * @return array
         * @see SHWCPPageTemplater::register_project_templates()
         * @since 0.0.1
         */
        public function register_project_templates( $atts ) {
            $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
            $templates = wp_get_theme()->get_page_templates();

            if( empty( $templates ) ) {
                $templates = array();
            } 

            wp_cache_delete( $cache_key , 'themes');
            $templates = array_merge( $templates, $this->posts_templates );
            wp_cache_add( $cache_key, $templates, 'themes', 1800 );

            return $atts;
        }

        /**
         * @internal Add our posts templates to the dropdown for WP v4.7+
         * @param array $posts_templates
         * @return array
         * @see SHWCPPageTemplater::add_new_template()
         * @since 0.0.1
         */
        public function add_new_template( $posts_templates ) {
            $posts_templates = array_merge( $posts_templates, $this->posts_templates );
            return $posts_templates;
        }

        /**
         * @internal Checks if the template is assigned to the page
         * @global WP_Post $post
         * @param array $template
         * @return array
         * @see SHWCPPageTemplater::view_project_template()
         * @since 0.0.1
         */
        public function view_project_template( $template ) {
            global $post;

            $post_id = isset( $post->ID ) ? $post->ID : '';
            $sel_tpl = get_post_meta( $post_id, '_wp_page_template', true );

            if( !isset( $this->templates[$sel_tpl] ) ) {
                return $template;        
            } 

            $file = plugin_dir_path( GLT_FILE) . 'src/' . $sel_tpl;

            if( file_exists( $file ) ) {
                return $file;
            } else {
                echo $file;
            }

            return $template;
        }

        /**
         * Enqueue CSS for our front page.
         * @return void
         * @see wcp_front::enqueue_styles()
         * @since 0.0.1
         */
        public function enqueue_styles() {
            parent::enqueue_styles();
            
            if( !is_page_template( GLT_TEMPLATE ) ) {
                return;
            }

            wp_register_style( 'odwpglt-front', plugins_url( '', GLT_FILE ) . '/assets/css/front.css', '1' );
            wp_enqueue_style( 'odwpglt-front' );
        }

        /**
         * Enqueue JavaScripts for our front page.
         * @return void
         * @see wcp_front::enqueue_scripts()
         * @since 0.0.1
         */
        public function enqueue_scripts() {
            parent::enqueue_scripts();
            
            if( !is_page_template( GLT_TEMPLATE ) ) {
                return;
            }

            wp_register_script( 'odwpglt-front', plugins_url( '', GLT_FILE ) . '/assets/js/front.js', array( 'jquery' ), 'GLT_VERSION', true );
            wp_enqueue_script( 'odwpglt-front' );
            wp_localize_script( 'odwpglt-front', 'odwpglt', array(
                // Put localized values here...
            ));
        }

		/**
		 * Early Check if our template is used on this page
         * @global wpdb $wpdb
         * @return boolean
         * @see main_wcp::template_early_check()
         * @since 0.0.1
		 */
		public function template_early_check() {
            $ret = parent::template_early_check();

            // Taken from the parent method.
			$template_used = false;
            $proto = is_ssl() ? 'https' : 'http';
            $url = $proto . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        	$postid = url_to_postid( $url );

			if( !$postid ) {
				$url_without_query = strtok( $url, '?' );
				$url_without_query = rtrim( $url_without_query, '/' );
				$site_url = get_site_url();
                
                if( $url_without_query == $site_url ) {
					$postid = get_option( 'page_on_front' );
				}
			}

        	global $wpdb;
            
            $template = $wpdb->get_var( $wpdb->prepare(
            	"select meta_value from $wpdb->postmeta WHERE post_id='%d' and meta_key='_wp_page_template';", $postid
        	) );
            
            if( $template == GLT_TEMPLATE ) {
				$template_used = true;
            }
            
			$database = get_post_meta( $postid, 'wcp_db_select', true );
			$this->early_check['postID'] = $postid;
			$this->early_check['template_used'] = $template_used;
            $this->early_check['database'] = $database;

			return $this->early_check;
    	}

		/**
		 * Load the frontend page content
         * @param string $filter
         * @return string
         * @see wcp_front::wcp_content_filter
         * @since 0.0.1
		 */
		public function wcp_content_filter( $content ) {
            $db = $this->load_db_options();

            if( !is_page_template( GLT_TEMPLATE ) ) {
                return $content;
            }

            global $wpdb;
            //...
            return 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXX';
        }

        /**
         * Replace original WCP JavaScript with our version.
         * @param string $hook
         * @return void
         * @since 0.0.1
         */
        public function admin_enqueue_scripts() {
            wp_dequeue_script( 'wcp-admin-meta' );
            wp_register_script( 'odwpglt-admin-meta', plugins_url( '', GLT_FILE ) . '/assets/js/admin-meta.js', '1' );
            wp_enqueue_script( 'odwpglt-admin-meta' );
        }
    }
endif;
