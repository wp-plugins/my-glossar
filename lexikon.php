<?php

/*
   Plugin Name: Glossar / Encyclopedia
   Plugin URI: http://www.benjamin-neske.de/stichwortlexikon-mit-wordpress-my-glossar-plugin/
   Description: Plugin to create a Glossar / Encyclopedia on Wordpress
   Author: Benjamin Neske
   Author URI: http://www.benjamin-neske.de
   Version: 1.0.2
*/
mb_internal_encoding("UTF-8");
class MyGlossar {


	// Constructor
	function __construct() {
		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'admin_menu', array( &$this, 'add_meta_box' ) );
		add_action( 'save_post', array( &$this, 'meta_box_save' ), 1, 2 );
		//Adminmenu
		add_action('admin_init', 'register_and_build_fields');
		add_action('admin_menu', 'theme_options_page');

		// activate the support for comments and author (backend)
		add_post_type_support( 'nessio_gl', array( 'comments', 'author' ) );
	}



	// PHP4 Constructor
	function MyGlossar() {
		$this->__construct();
	}

	function register_post_type() {

		$options = get_option('theme_options');

		register_post_type( 'nessio_gl',
			array(
				'labels' => array(
					'name' => __( 'My Glossar' ),
					'singular_name' => __( 'term' ),
					'add_new' => __( 'Add New' ),
					'add_new_item' => __( 'Add New Term' ),
					'edit' => __( 'Edit' ),
					'edit_item' => __( 'Edit Term' ),
					'new_item' => __( 'New Term' ),
					'view' => __( 'View Term' ),
					'view_item' => __( 'View Term' ),
					'search_items' => __( 'Search Term' ),
					'not_found' => __( 'No Terms found' ),
					'not_found_in_trash' => __( 'No Terms found in Trash' )
				),
				'public' => true,
				'query_var' => true,
				'show_in_menu' => true,
				'show_ui' => true,
				'supports' => array( 'title' ),
				'rewrite' => array( 'slug' => $options['slug_setting'] ? $options['slug_setting'] : 'lexikon', 'with_front' => false )
			)
		);

	}


	function add_meta_box() {
		add_meta_box('nessio_gl', __('Description', 'nessio_gl'), array( &$this, 'meta_box' ), 'nessio_gl', 'normal', 'high');
	}

	function meta_box() {
		global $post;

		printf( '<input type="hidden" name="_nessio_gl_nonce" value="%s" />', wp_create_nonce( plugin_basename(__FILE__) ) );

		printf( '<p><label for="%s">%s</label></p>', '_nessio_gl_description', __('Descprition of the Terms', 'nessio_gl') );
		//Enable the Editor / TinyMCE
		$settings = array( 'media_buttons' => true );
		wp_editor( get_post_meta( $post->ID, '_nessio_gl_term', true ), '_nessio_gl_term', $settings );
	}

	function meta_box_save( $post_id, $post ) {

		$key = '_nessio_gl_term';

		//	verify the nonce
		if ( !isset($_POST['_nessio_gl_nonce']) || !wp_verify_nonce( $_POST['_nessio_gl_nonce'], plugin_basename(__FILE__) ) )
			return;

		//	don't try to save the data under autosave, ajax, or future post.
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;
		if ( defined('DOING_AJAX') && DOING_AJAX ) return;
		if ( defined('DOING_CRON') && DOING_CRON ) return;

		//	is the user allowed to edit the URL?
		if ( ! current_user_can( 'edit_posts' ) || $post->post_type != 'nessio_gl' )
			return;

		$value = isset( $_POST[$key] ) ? $_POST[$key] : '';

		if ( $value ) {
			//	save/update
			$my_post = array();
			update_post_meta($post->ID, $key, $value);


		} else {
			//	delete if blank
			delete_post_meta($post->ID, $key);
		}

	}

};




function gl_direct() {
	$html = '<div>';
	$args = array( 'post_type' => 'nessio_gl',  'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => '-1');
	$loop = new WP_Query( $args );

	$aktueller_anfangsbuchstabe = null;
	$aktueller_anfangsbuchstabe2 = null;

	if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post();
		$title = get_the_title();

		if($title):
			$entry_anfangsbuchstabe = strtoupper(mb_substr($title, 0, 1));
			$isFirstCharLetter = ctype_alpha($entry_anfangsbuchstabe);
			$permalink = get_permalink();

			if($isFirstCharLetter == True) {
				if (is_null($aktueller_anfangsbuchstabe) || $aktueller_anfangsbuchstabe != $entry_anfangsbuchstabe) {

					$html .= '<a name="'.$entry_anfangsbuchstabe.'"></a><h3 style="border-bottom:1px solid #EBEBEB; clear:both;"><span style="margin:0 0 10px 10px;">'.$entry_anfangsbuchstabe.'</span> </h3>';
					$aktueller_anfangsbuchstabe = $entry_anfangsbuchstabe;
				}
				$html .= '<p style="float:left; width:205px;"><a href="'.$permalink.'">'.$title.'</a></p>';
			} else {
				$headline = true;
				/* if (is_null($aktueller_anfangsbuchstabe2) || $aktueller_anfangsbuchstabe2 != $entry_anfangsbuchstabe) {
                        $headline = true;
                        //$nhtml .= '<h3 style="border-bottom:1px solid #EBEBEB; clear:both;"><span style="margin:0 0 10px 10px;">'.$entry_anfangsbuchstabe.'</span> </h3>';
                        $aktueller_anfangsbuchstabe2 = $entry_anfangsbuchstabe;
                    }*/

				$nhtml .= '<p style="float:left; width:205px;"><a href="'.$permalink.'">'.$title.'</a></p>';
			}
		endif;
	endwhile;
	endif;
	if ($headline) {
		$html .= '<a name="all"></a><h3 style="border-bottom:1px solid #EBEBEB; clear:both;"><span style="margin:0 0 10px 10px;">#</span> </h3>';
	}
	$html .= $nhtml;
	$html .= '</div>';
	return $html;

}

add_shortcode('gl_directory', 'gl_direct'); //Workarround for older PHP-Versions - Do not use function() instead of gl_direct


function gl_nav() {
	$html = '';
	$args = array( 'post_type' => 'nessio_gl',  'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => '-1'  );
	$loop = new WP_Query( $args );
	$array = array();
	if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post();
		$str = get_the_title();
		$array[] = strtoupper(substr($str, 0, 1));
	endwhile;
	endif;

	$clean_array = array_unique($array);
	for($i = 65; $i < 91; $i++) {

		if(in_array(chr($i), $clean_array)) {
			$html .= '<span style="float:left;font-size:18px; margin-left:10px; display: inline;"><a href="#'.chr($i).'">'.chr($i).'</a></span>';

		} else {
			$html .= '<span style="float:left;font-size:18px; margin-left:10px; display: inline; color:#d3d3d3;">'.chr($i).'</span>';
		}
	}

	if(is_numeric($clean_array[0]) OR $clean_array[0] != chr(65)) {
		$html .= '<span style="float:left;font-size:18px; margin-left:10px; display: inline;"><a href="#all">#</a></span>';
	}
	$html .='<div style="clear:left;"></div>';
	return $html;
};

add_shortcode('gl_navigation', 'gl_nav'); //Workarround for older PHP-Versions - Do not use function() instead of gl_nav


function pippin_filter_content_sample($content) {
	if( is_singular('nessio_gl') && is_main_query() ) {
		$new_content = get_post_meta( get_the_ID(), '_nessio_gl_term' );
		if($new_content):
			$content .= wpautop($new_content[0]);
		endif;
	}
	return $content;
};
add_filter('the_content', 'pippin_filter_content_sample');


$MyGlossar = new MyGlossar;

// Adminmenu

function build_options_page() { ?>
	<div id="theme-options-wrap">
		<div class="icon32" id="icon-tools"> <br /> </div>
		<h2>My Gloassar Settings</h2>

		<form method="post" action="options.php" enctype="multipart/form-data">
			<?php settings_fields('theme_options'); ?>
			<?php do_settings_sections(__FILE__); ?>
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
		</form>
		<p>After editing the Slug the Permalinks Settings must be saved again.</p>
	</div>
<?php }

function register_and_build_fields() {
	register_setting('theme_options', 'theme_options', 'validate_setting');
	add_settings_section('general_settings', 'General Settings', 'settings_general', __FILE__);

	function settings_general() {}

	add_settings_field('slug', 'Url-Slug', 'slug_setting', __FILE__, 'general_settings');
}

function validate_setting($theme_options) {
	return $theme_options;
}

function slug_setting() {
	$options = get_option('theme_options'); echo "<input name='theme_options[slug_setting]' type='text' value='{$options['slug_setting']}' />";
}


function theme_options_page() { add_options_page('Glossar', 'Glossar', 'administrator', __FILE__, 'build_options_page');}
?>