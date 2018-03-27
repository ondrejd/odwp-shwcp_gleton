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

?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width">
<?php
/*
 * Title tag support for older versions of WP (pre 4.1)
 */
if ( ! function_exists( '_wp_render_title_tag' ) ) {
    function wcp_slug_render_title() {
	?>
		<title><?php wp_title( '-', true, 'right' ); ?></title>
	<?php
	}
    add_action( 'wp_head', 'wcp_slug_render_title' );
}
?>
    <link rel="profile" href="http://gmpg.org/xfn/11">
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<?php the_content(); ?>

<?php wp_footer(); ?>

<?php /* User generated js */ 

$db = '';
$database = get_post_meta(get_the_ID(), 'wcp_db_select', true);
if ($database && $database != 'default') {
	$db = '_' . $database;
}
$first_tab = get_option('shwcp_main_settings' . $db);
?>
<?php if (isset($first_tab['custom_js']) && trim($first_tab['custom_js']) != '') { ?>
    <script>
        <?php echo $first_tab['custom_js'] . "\n"; // User javascript ?>
    </script>
<?php } ?>
</body>
</html>

