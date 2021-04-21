<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
/**
 * Veloxserv functions and definitions
 *
 * @package Veloxserv
 * @subpackage veloxserv
 * @since veloxserv 1.0
 */

 /**
 * Table of Contents:
 * Theme Support
 * Required Files
 * Register Styles & Scripts
 * Register Menus
 * Custom Logo
 * Register Sidebars
 * Menu Class
 * Wp Nav Walker
 * Custom Excerpt
 * Add Custom Favicon
 * Admin Login Page
 * Remove unsual P tags
 * Tablepress custom output
*/

/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
*/

function veloxserv_theme_support() {

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );

	// Custom logo.
	$logo_width  = 120;
	$logo_height = 90;

	// If the retina setting is active, double the recommended width and height.
	if ( get_theme_mod( 'retina_logo', false ) ) {
		$logo_width  = floor( $logo_width * 2 );
		$logo_height = floor( $logo_height * 2 );
	}

	add_theme_support(
		'custom-logo',
		array(
			'height'      => $logo_height,
			'width'       => $logo_width,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	add_theme_support( 'title-tag' );

	/*
	 * Switch default core markup for search form, comment form, and comments
	 * to output valid HTML5.
	 */
	add_theme_support(
		'html5',
		array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'script',
			'style',
		)
	);

	/*
	 * Make theme available for translation.
	 * Translations can be filed in the /languages/ directory.
	 * If you're building a theme based on Veloxserv, use a find and replace
	 * to change 'veloxserv' to the name of your theme in all the template files.
	 */
	load_theme_textdomain( 'veloxserv' );

	// Add support for full and wide align images.
	add_theme_support( 'align-wide' );

	// Add support for responsive embeds.
	add_theme_support( 'responsive-embeds' );

	// Add theme support for selective refresh for widgets.
    add_theme_support( 'customize-selective-refresh-widgets' );
    
	//Custom Image Sizes
	add_image_size( 'testimonial-thumbnail', 120, 39, false );
	add_image_size( 'post-thumbnail', 243, 185, true );
	
    // Options Page
    if( function_exists('acf_add_options_page') ) {
	
        acf_add_options_page();
        
    }

}

add_action( 'after_setup_theme', 'veloxserv_theme_support' );

/**
 * REQUIRED FILES
 * Include required files.
 */

//Custom-post
get_template_part('inc/custom-post/testimonial');

// Shortcodes
get_template_part('inc/shortcode/theme','shortcode');

/**
 * Register and Enqueue Styles & Scripts.
 */
function veloxserv_register_styles_and_scripts() {

    $theme_version = wp_get_theme()->get( 'Version' );
    $rand=rand();
    
    wp_enqueue_style( 'veloxserv-carousel', get_template_directory_uri().'/assets/css/vendor/owl.carousel.min.css"', array(), $theme_version );
    wp_enqueue_style( 'veloxserv-scrollbar', get_template_directory_uri().'/assets/css/vendor/jquery.mCustomScrollbar.css"', array(), $theme_version );
    wp_enqueue_style( 'veloxserv-main', get_template_directory_uri().'/assets/css/vendor/main.css"', array(), $theme_version );
    wp_enqueue_style( 'veloxserv-css', get_template_directory_uri().'/assets/css/style.css"', array(), $rand );
    wp_enqueue_style( 'veloxserv-responsive', get_template_directory_uri().'/assets/css/responsive.css"', array(), $rand );
    wp_enqueue_style( 'veloxserv-style', get_stylesheet_uri(), array(), $rand );
    
    wp_enqueue_script('veloxserv-modernizer',get_template_directory_uri()."/assets/js/vendor/modernizr.min.js",array(),$theme_version,false);
    wp_enqueue_script('veloxserv-jquery',get_template_directory_uri()."/assets/js/vendor/jquery.js",array(),$theme_version,false);
    wp_enqueue_script('veloxserv-matchHeight',get_template_directory_uri()."/assets/js/vendor/jquery.matchHeight-min.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-carouseljs',get_template_directory_uri()."/assets/js/vendor/owl.carousel.min.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-mainjs',get_template_directory_uri()."/assets/js/vendor/main.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-components',get_template_directory_uri()."/assets/js/vendor/components.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-runtime',get_template_directory_uri()."/assets/js/vendor/runtime_main.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-vendor_components',get_template_directory_uri()."/assets/js/vendor/vendors_components.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-Scrollbar-js',get_template_directory_uri()."/assets/js/vendor/jquery.mCustomScrollbar.concat.min.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-webticker',get_template_directory_uri()."/assets/js/vendor/jquery.webticker.min.js",array(),$theme_version,true);
    wp_enqueue_script('veloxserv-general',get_template_directory_uri()."/assets/js/general.js",array(),$rand,true);

}

add_action( 'wp_enqueue_scripts', 'veloxserv_register_styles_and_scripts' );

/**
 * Register navigation menus uses wp_nav_menu.
 */
function veloxserv_menus() {

    $locations = array(
            'primary'  => __( 'Primary Menu', 'veloxserv' ),
            'footer'   => __('Footer Menu','veloxserv'),
    );

    register_nav_menus( $locations );
}

add_action( 'init', 'veloxserv_menus' );

/**
 * Register widget areas.
 */
function veloxserv_sidebar_registration() {

	// Arguments used in all register_sidebar() calls.
	$shared_args = array(
		'before_title'  => '<h2>',
		'after_title'   => '</h2>',
		'before_widget' => ' ',
		'after_widget'  => ' ',
	);

	// Footer #1.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #1', 'veloxserv' ),
				'id'          => 'sidebar-1',
				'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'veloxserv' ),
			)
		)
	);

	// Footer #2.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #2', 'veloxserv' ),
				'id'          => 'sidebar-2',
				'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'veloxserv' ),
			)
		)
	);

	// Footer #3.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #3', 'veloxserv' ),
				'id'          => 'sidebar-3',
				'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'veloxserv' ),
			)
		)
	);

	// Footer #4.
	register_sidebar(
		array_merge(
			$shared_args,
			array(
				'name'        => __( 'Footer #4', 'veloxserv' ),
				'id'          => 'sidebar-4',
				'description' => __( 'Widgets in this area will be displayed in the first column in the footer.', 'veloxserv' ),
			)
		)
	);
}

/*
* Adding Class li in Wp nav Menu
*/
add_action( 'widgets_init', 'veloxserv_sidebar_registration' );

function veloxserv_menu_classes($classes, $item, $args) {
	if($args->theme_location == 'primary') {
		if(in_array("menu-item-has-children",$classes))
	   	{
                    $classes[] = 'nav__level-1-li';
	   	}
	}
	return $classes;
  }
add_filter('nav_menu_css_class', 'veloxserv_menu_classes', 1, 3);

/**
 * Wp Nav Walker for Primary Menu
*/

class velox_submenu_Walker extends Walker_Nav_Menu
{
	public $i=0;
	public $curlItem;
    function start_lvl( &$output, $depth = 0, $args = array() ) {
		$this->i++;
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<span class='nav-span' data-menu='nav-main-".$this->i."'></span><div class='nav__level-2-wrapper nav-main-".$this->i."'><div class='site-wide'>
        <div class='nav__title-2'>
            <h4>".get_field('veloxserv_menu_heading',$this->curlItem)."</h4>
            <p>".get_field('veloxserv_menu_description',$this->curlItem)."</p>
        </div>
		<div class='nav__level-2'><ul class='sub-menu'>\n";
    }
    function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul></div></div></div>\n";
	}
	
	function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
        if ( isset( $args->item_spacing ) && 'discard' === $args->item_spacing ) {
            $t = '';
            $n = '';
        } else {
            $t = "\t";
            $n = "\n";
        }
        $indent = ( $depth ) ? str_repeat( $t, $depth ) : '';
 
        $classes   = empty( $item->classes ) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
 
        /**
         * Filters the arguments for a single nav menu item.
         *
         * @since 4.4.0
         *
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param WP_Post  $item  Menu item data object.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );
 
        /**
         * Filters the CSS classes applied to a menu item's list item element.
         *
         * @since 3.0.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
 
        /**
         * Filters the ID applied to a menu item's list item element.
         *
         * @since 3.0.1
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
         * @param WP_Post  $item    The current menu item.
         * @param stdClass $args    An object of wp_nav_menu() arguments.
         * @param int      $depth   Depth of menu item. Used for padding.
         */
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
 
        $output .= $indent . '<li' . $id . $class_names . '>';
 
        $atts           = array();
        $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
        $atts['target'] = ! empty( $item->target ) ? $item->target : '';
        if ( '_blank' === $item->target && empty( $item->xfn ) ) {
            $atts['rel'] = 'noopener';
        } else {
            $atts['rel'] = $item->xfn;
        }
        $atts['href']         = ! empty( $item->url ) ? $item->url : '';
        $atts['aria-current'] = $item->current ? 'page' : '';
 
        /**
         * Filters the HTML attributes applied to a menu item's anchor element.
         *
         * @since 3.6.0
         * @since 4.1.0 The `$depth` parameter was added.
         *
         * @param array $atts {
         *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
         *
         *     @type string $title        Title attribute.
         *     @type string $target       Target attribute.
         *     @type string $rel          The rel attribute.
         *     @type string $href         The href attribute.
         *     @type string $aria_current The aria-current attribute.
         * }
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );
 
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
                $value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
 
        /** This filter is documented in wp-includes/post-template.php */
        $title = apply_filters( 'the_title', $item->title, $item->ID );
 
        /**
         * Filters a menu item's title.
         *
         * @since 4.4.0
         *
         * @param string   $title The menu item's title.
         * @param WP_Post  $item  The current menu item.
         * @param stdClass $args  An object of wp_nav_menu() arguments.
         * @param int      $depth Depth of menu item. Used for padding.
         */
        $title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );
 
        $item_output  = $args->before;
        $item_output .= '<a' . $attributes . '>';
		// Custom Before Title
		$veloxserv_before_menu = $depth!=0 ? '<img src="'.get_template_directory_uri().'/assets/images/server.png" alt="server">' : '';
        $item_output .=  $veloxserv_before_menu . $args->link_before . $title . $args->link_after;
        $item_output .= '</a>';
        $item_output .= $args->after;
 
        /**
         * Filters a menu item's starting output.
         *
         * The menu item's starting output only includes `$args->before`, the opening `<a>`,
         * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
         * no filter for modifying the opening and closing `<li>` for a menu item.
         *
         * @since 3.0.0
         *
         * @param string   $item_output The menu item's starting HTML output.
         * @param WP_Post  $item        Menu item data object.
         * @param int      $depth       Depth of menu item. Used for padding.
         * @param stdClass $args        An object of wp_nav_menu() arguments.
         */
		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
		$this->curlItem=$item->ID;
    }
}


function veloxserv_remove_brackets($content) {
	return str_replace('[&hellip;]', ' ', $content);
}
add_filter('the_excerpt', 'veloxserv_remove_brackets');
add_filter('get_the_excerpt', 'veloxserv_remove_brackets');

function veloxserv_custom_excerpt_length($length)
{
	return 15;
}
add_filter('excerpt_length','veloxserv_custom_excerpt_length');

function veloxserv_add_favicon(){ ?>
    <!-- Custom Favicons -->
    <link rel="shortcut icon" href="<?php the_field('veloxserv_favicon','options'); ?>" type="image/x-icon" />
    <?php }
add_action('wp_head','veloxserv_add_favicon');
add_action('admin_head','veloxserv_add_favicon');
add_action('login_head','veloxserv_add_favicon');

//To change Veloxserv admin wordpress logo
function veloxserv_login_logo_url() {
	return home_url('/');
}
add_filter( 'login_headerurl', 'veloxserv_login_logo_url' );
	
//To change url title in wp-admin login screen
function veloxserv_login_logo_url_title() {
    return get_bloginfo( 'name' );
}
add_filter( 'login_headertext', 'veloxserv_login_logo_url_title' );

if ( ! function_exists( 'veloxserv_login_customize' ) ) :
	/**
	* Override Login Page Styles
	* Handles to login logo
	* @subpackage olxpeople
	**/
	function veloxserv_login_customize() { ?>
		<style>
		body.login div#login h1 a {
		background-image: url('<?php the_field('veloxserv_logo','option'); ?>');
		background-size: 100% auto;
		background-position: center center;
		}

		#loginform #wp-submit {
		background-color: #0e285a;
		margin-top:5px;
		border: none;
		border-radius: 3px;
		color: #fff;
		display: inline-block;
		font-size: 16px;
		font-weight: 600;
		padding: 5px 15px 5px 15px;
		text-decoration: none;
		text-transform: uppercase;
		text-shadow: none;
		height: auto;
		line-height: normal;
		box-shadow: none;
		}
			#loginform input {
				outline-color:#de097d;
				border-color:none;
			}
			#loginform .dashicons {
				color:#474747;
			}
		#loginform #wp-submit:hover {
		background-color: #de097d;
		color: #fff;
		text-decoration: none;
		}
		.login form .forgetmenot label { padding-top: 10px; }
		</style>
		<?php
		}
	add_action( 'login_enqueue_scripts', 'veloxserv_login_customize' );
endif; 

//Remove Unusual P Tags
function remove_empty_p( $content ){
    // clean up p tags around block elements
    $content = preg_replace( array(
        '#<p>\s*<(div|aside|section|article|header|footer)#',
        '#</(div|aside|section|article|header|footer)>\s*</p>#',
        '#</(div|aside|section|article|header|footer)>\s*<br ?/?>#',
        '#<(div|aside|section|article|header|footer)(.*?)>\s*</p>#',
        '#<p>\s*</(div|aside|section|article|header|footer)#',
    ), array('<$1', '</$1>', '</$1>', '<$1$2>','</$1',), $content );
    return preg_replace('#<p>(\s|&nbsp;)*+(<br\s*/*>)*(\s|&nbsp;)*</p>#i', '', $content);
}
add_filter( 'the_content', 'remove_empty_p', 20, 1 );
	

function veloxserv_disable_emojis() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	add_filter( 'tiny_mce_plugins', 'veloxserv_disable_emojis_tinymce' );
}
add_action( 'init', 'veloxserv_disable_emojis' );
/**
* Filter function used to remove the tinymce emoji plugin.
*
* @param array $plugins
* @return array Difference betwen the two arrays
*/
function veloxserv_disable_emojis_tinymce( $plugins ) {
if ( is_array( $plugins ) ) {
return array_diff( $plugins, array( 'wpemoji' ) );
} else {
return array();
}
}

add_filter('tablepress_table_output','veloxserv_tablepress_table_output');
function veloxserv_tablepress_table_output($output) {

	return '<div class="server-details-table-sec">
		<div class="wrap">
			<div class="cols cols1 default-grid">
				<div class="col"> 
					<div class="server-details-table content mCustomScrollbar _mCS_1" data-mcs-axis="x">
					'.$output.'
					</div>
				</div>
			</div>
		</div>
	</div>';
}
