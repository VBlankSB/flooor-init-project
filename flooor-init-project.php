<?php
/*
Plugin Name: Flooor Init project
Description: Nettoyage et optimisation pour le demarrage d'un projet wp avec theme Flooor
Version: 1.0
Author: Valerie Blanchard
Text Domain: flooor-init-project
License: GPL2 or later
Domain Path: /languages
*/

/*
This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define( 'FLOOORINIT_VERSION', '1.0' );

/**
 * Add textdomain hook for translation
 */
add_action( 'plugins_loaded', 'flooor_ip_setup' );
function flooor_ip_setup() {

	do_action( 'flooor_ip_setup_pre' );

	load_plugin_textdomain( 'flooor-init-project', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

/**
 * Remove WordPress widgets if unnecessary
 * =======================================
 *
 * If you do not need some default WordPress widgets, you can simply disable them to prevent them from being loaded by WordPress and save a few queries.
 * Since WP default priority is 10, setting this to 11 will assure this action overrides the default, it comes after.
 * Uncomment what you want to remove
 */

if ( ! function_exists( 'flooor_unregister_default_widgets' ) ) {
	function flooor_unregister_default_widgets() {
	// unregister_widget('WP_Widget_Pages');
	// unregister_widget('WP_Widget_Calendar');
	// unregister_widget('WP_Widget_Archives');
	// unregister_widget('WP_Widget_Links');
	// unregister_widget('WP_Widget_Meta');
	// unregister_widget('WP_Widget_Search');
	// unregister_widget('WP_Widget_Text');
	// unregister_widget('WP_Widget_Media_Audio');
	// unregister_widget('WP_Widget_Media_Image');
	// unregister_widget('WP_Widget_Media_Video');
	// unregister_widget('WP_Widget_Custom_HTML');
	// unregister_widget('WP_Widget_Categories');
	// unregister_widget('WP_Widget_Recent_Posts');
	// unregister_widget('WP_Widget_Recent_Comments');
	// unregister_widget('WP_Widget_RSS');
	// unregister_widget('WP_Widget_Tag_Cloud');
	// unregister_widget('WP_Nav_Menu_Widget');
	}
	add_action( 'widgets_init', 'flooor_unregister_default_widgets', 11 );
}

/**
 * Removes the administrator’s bar
 * and also the associated CSS styles, especially during development.
 *
 * @link https://codex.wordpress.org/Plugin_API/Filter_Reference/show_admin_bar
 * ============================================================================
 */
add_filter( 'show_admin_bar', '__return_false' );


/**
 * Removes emoji support
 * =====================
 */
function flooor_no_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
}
add_action( 'after_setup_theme', 'flooor_no_emoji' );

/**
 * If emoji is disabled, remove link dns-prefetch //s.w.org in head
 *
 * @link https://wordpress.org/support/topic/remove-the-new-dns-prefetch-code/
 * @link https://developer.wordpress.org/reference/functions/wp_resource_hints/
 */
add_filter( 'emoji_svg_url', '__return_false' );
// This way works too :
// function remove_dns_prefetch( $hints, $relation_type ) {
//     if ( 'dns-prefetch' === $relation_type ) {
//         $matches = preg_grep('/emoji/', $hints);
//    return array_diff( $hints, $matches );
//     }

//     return $hints;
// }
// add_filter( 'wp_resource_hints', 'remove_dns_prefetch', 10, 2 );


/**
 * Handles JavaScript detection
 * ============================
 *
 * Script for no-js / js class if you don't use Modenizr
 * Replaces 'no-js' by a `js` class in the root `<html>` element when JavaScript is detected.
 * To use it add class="no-js" on html tag
 */
function flooor_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js');})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'flooor_javascript_detection', 0 );


/**
 * Generate custom search form = NOT USED adding searchform.php prefered
 * =====================================================================
 *
 * @param string $form Form HTML.
 * @return string Modified form HTML.
 * @link https://developer.wordpress.org/reference/functions/get_search_form/
 */
// function flooor_my_search_form( $form ) {
//     $form = '<form role="search" method="get" class="search-form" action="' . home_url( '/' ) . '" >
//     <label class="screen-reader-text" for="s">' . __( 'Search for:', 'flooor-init-project' ) . '</label>
//     <input type="search" class="search-field" value="' . get_search_query() . '" name="s" placeholder="' . esc_attr_x( 'Search …', 'placeholder', 'flooor-init-project' ) . '"/>
//     <input type="submit" class="search-submit" value="'. esc_attr__( 'Search', 'flooor-init-project' ) .'" />
//     </form>';

//     return $form;
// }
// add_filter( 'get_search_form', 'flooor_my_search_form' );


/**
 * Halt the main query in case of an empty search
 * ==============================================
 *
 * @link https://wordpress.stackexchange.com/questions/216694/empty-search-input-returns-all-posts
 */
add_filter( 'posts_search', function( $search, \WP_Query $q ) {
	if ( ! is_admin() && empty( $search ) && $q->is_search() && $q->is_main_query() ) {
		$search .= ' AND 0=1 ';
	}
	return $search;
}, 10, 2 );


/**
 * Removes h1 in WP Editor
 * =======================
 */
function flooor_remove_h1_wp_editor( $init ) {
	$init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3;Heading 4=h4;Heading 5=h5;Heading 6=h6;Pre=pre';
	return $init;
}
add_filter( 'tiny_mce_before_init', 'flooor_remove_h1_wp_editor' );


/**
 * Manages images in tiny MCE
 * ==========================
 * By default WP adds a <p> around <img>
 *
 * You can:
 * 1- Replace <p> around <img> by <figure>
 * @link https://www.pushaune.com/blog/retirer-balises-paragraphe-autour-dune-image-wordpress/
 *
 * 2- OR Remove <p> around <img>
 * @link https://css-tricks.com/snippets/wordpress/remove-paragraph-tags-from-around-images/
 *
 * Uncomment which one you need
 */
// 1- Replace <p> around <img> by <figure>
/*add_filter( 'the_content', 'flooor_replace_p_in_img', 99 );
function flooor_replace_p_in_img( $content ) {
   $content = preg_replace(
      '/<p>\\s*?(<a rel=\"attachment.*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s',
      '<figure>$1</figure>',
      $content
   );
   return $content;
}*/

// 2- Remove <p> around <img>
add_filter( 'the_content', 'flooor_filter_ptags_on_images', 99 );
function flooor_filter_ptags_on_images( $content ) {
	return preg_replace( '/<p>\\s*?(<a .*?><img.*?><\\/a>|<img.*?>)?\\s*<\\/p>/s', '\1', $content );
}


/**
 * Manage meta description
 * =======================
 * WordPress doesn't include meta tags such as description and keywords,
 * It manages only title-tags with
 * add_theme_support( 'title-tag' ); added in functions.php in hook 'after_setup_theme'
 * @link https://codex.wordpress.org/Meta_Tags_in_WordPress
 *
 * Instead of using the same description on all pages in head like so :
 * <meta name="description" content="<?php bloginfo('description'); ?>">
 * Manage it !
 *
 * Use it in your header.php like so:
 * <?php if (function_exists('flooor_generate_description')) :
 *   $desc = flooor_generate_description();
 *   if ( $desc ): ?>
 *     <meta name="description" content="<?php echo $desc ?>">
 *   <?php endif;
 * endif;?>
 *
 * NO NEED this if you use SEO Plugin !!!
 */
function flooor_generate_description() {
	$description = '';

	if ( is_home() || is_front_page() ) {
		$description = get_bloginfo( 'description' );
	} elseif ( is_single() || is_page() ) {
		global $post;
		if ( empty( $post->post_excerpt ) ) {
			// $post->post_excerpt will only return user-created excerpt
			// So generate manually an excerpt
			// wp_trim_words( $content, $num_words, $more );  $num_words: default to 55 words $more: default to hellip
			$text = wp_trim_words( $post->post_content, 30, '' );
			$description = wp_kses_post( $text );
		} else {
			// Retrieve the excerpt
			$description = wp_kses_post( $post->post_excerpt );
		}
	} elseif ( is_category() || is_tag() ) {
		$name = get_bloginfo( 'name' );
		$category = single_cat_title( '', false );
		//$description = 'List of articles published on ' . $name . ' filed under category : ' . $category ;
		// with traduction
		$description = __( 'List of articles published on ', 'flooor-init-project' ) . $name . __( ' filed under category : ', 'flooor-init-project' ) . $category;
	}

	//return esc_html($description);
	return $description;
}
add_action( 'wp_head', 'floor_insert_description', 1 );
function floor_insert_description() {
	$desc = flooor_generate_description();
	$metadesc = '<meta name="description" content="' . $desc . '" >' . "\n";

	echo $metadesc;
}


/**
 * Adds custom classes to the array of body classes
 * ================================================
 * Comes from Underscores
 * @link https://github.com/Automattic/_s/blob/master/inc/template-functions.php
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function flooor_body_classes( $classes ) {
	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	return $classes;
}
add_filter( 'body_class', 'flooor_body_classes' );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles
 * =============================================================================
 * Comes from Underscores
 *
 * @link https://github.com/Automattic/_s/blob/master/inc/template-functions.php
 */
function flooor_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'flooor_pingback_header' );


/**
 * Readmore link wp accessibility
 * ==============================
 * @link https://github.com/wpaccessibility/a11ythemepatterns/blob/master/read-more-links/functions.php
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and a 'Continue reading' link.
 *
 * @param string $link Link to single post/page.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
if ( ! function_exists( 'flooor_excerpt_more' ) && ! is_admin() ) :

	function flooor_excerpt_more( $link ) {
		if ( is_admin() ) {
			return $link;
		}

		$link = sprintf( '<a href="%1$s" class="more-link">%2$s</a>',
			esc_url( get_permalink( get_the_ID() ) ),
			/* translators: %s: Name of current post */
			sprintf( __( 'Continue reading %s', 'flooor-init-project' ), '<span class="screen-reader-text">' . get_the_title( get_the_ID() ) . '</span>' )
		);
		return ' &hellip; ' . $link;
	}

	add_filter( 'excerpt_more', 'flooor_excerpt_more' );
endif;


/**
 * Moves jQuery to the footer
 * ==========================
 * Adding his own jQuery is not necessary WP automatically loads jQuery whenever other script requires it.
 * If declare jQuery as a dependency for a script, WordPress automatically will load its own copy of jQuery.
 * wp_enqueue_script( 'jquery' ); => Not necessary
 * Instead write :
 *  wp_enqueue_script( 'name-of-your-script', get_template_directory_uri() . '/js/myscript.js', array('jquery'), YOURTHEME_VERSION, true );
 *  NB : param true insert your script in footer but jQuery is loaded in header by default
 *
 *  @link https://digwp.com/2009/06/including-jquery-in-wordpress-the-right-way/
 *  @link http://wordpress.stackexchange.com/questions/173601/enqueue-core-jquery-in-the-footer
 */
function flooor_move_jquery_scripts() {
	wp_scripts()->add_data( 'jquery', 'group', 1 );
	wp_scripts()->add_data( 'jquery-core', 'group', 1 );
	wp_scripts()->add_data( 'jquery-migrate', 'group', 1 );
}
add_action( 'wp_enqueue_scripts', 'flooor_move_jquery_scripts' );


/**
 * Upload jQuery version of your choice
 * ====================================
 * WP comes with an old version of jQuery, it’s for compatibility with ≤IE8 mostly
 * if you don’t need it and you prefer a more recent branch uncomment this function below
 * Load jQuery in head with parameter false, or at the end with true
 */
/*
function flooor_upload_own_jquery() {
	wp_deregister_script('jquery');
	wp_register_script('flooor-jquery', 'https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js', array(), '3.2.1', false);
	wp_enqueue_script('flooor-jquery');
}
add_action( 'wp_enqueue_scripts', 'flooor_upload_own_jquery' );
*/

/**
 * Emphase results search Words
 * ============================
 * @link https://wabeo.fr/formulaire-recherche-multi-criteres/
 */

add_filter( 'the_title', 'flooor_emphase_search_words' );
add_filter( 'the_excerpt', 'flooor_emphase_search_words' );
function flooor_emphase_search_words( $content ) {
	if ( is_search()
		&& in_the_loop()
		&& is_main_query() ) {
		if ( preg_match_all( '/".*?("|$)|((?<=[\t ",+])|^)[^\t ",+]+/', get_search_query(), $matches ) ) {
			$stopwords = explode( ',', _x( 'about,an,are,as,at,be,by,com,for,from,how,in,is,it,of,on,or,that,the,this,to,was,what,when,where,who,will,with,www',
			'Comma-separated list of search stopwords in your language' ) );
			$terms     = array_diff( $matches[0], $stopwords );
			$content   = str_ireplace( $terms, array_map( 'flooor_add_mark', $terms ), $content );
		}
	}
	return $content;
}

function flooor_add_mark( $elem ) {
	return '<mark>' . $elem . '</mark>';
}
