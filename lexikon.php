<?php

/*
   Plugin Name: Glossar / Encyclopedia
   Plugin URI: http://www.benjamin-neske.de/stichwortlexikon-mit-wordpress-my-glossar-plugin/
   Description: Plugin to create a Glossar / Encyclopedia on Wordpress
   Author: Benjamin Neske
   Author URI: http://www.benjamin-neske.de
   Version: 0.9.5
*/

class MyGlossar {
	
	// Constructor
	function __construct() {
		add_action( 'init', array( &$this, 'register_post_type' ) );
		add_action( 'admin_menu', array( &$this, 'add_meta_box' ) );
		add_action( 'save_post', array( &$this, 'meta_box_save' ), 1, 2 );

		// activate the support for comments and author (backend)
		add_post_type_support( 'nessio_gl', array( 'comments', 'author' ) );
	}		
	
	// PHP4 Constructor
	function MyGlossar() {
		$this->__construct();
	}	
	
	function register_post_type() {		

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
				'rewrite' => array( 'slug' => 'lexikon', 'with_front' => false )
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


	add_shortcode('gl_directory', function() {
		$html = '<div>';
			$args = array( 'post_type' => 'nessio_gl',  'orderby' => 'title', 'order' => 'ASC', 'posts_per_page' => '-1');
			$loop = new WP_Query( $args );
			$i = 64;

		if ($loop->have_posts()) : while ($loop->have_posts()) : $loop->the_post();
			$title = get_the_title();
			$permalink = get_permalink();

			if($title):
				//Workarround Germane Special Characters
				$umlaute = array('€', '…', '†', 'Š', 'š', 'Ÿ');
				$letter = array('A', 'O', 'U', 'a', 'o', 'u');
				$str = strtoupper(substr(str_replace($umlaute, $letter, $title), 0, 1));
								
				if (chr($i) == $str) {
					$html .= '<p style="float:left; width:205px;"><a href="'.$permalink.'">'.$title.'</a></p>';
				} else {
		
					while (chr($i) != $str) {
						$i++;}
					$html .= '<a name="'.chr($i).'"></a><h3 style="border-bottom:1px solid #EBEBEB; clear:both;"><span style="margin:0 0 10px 10px;">'.chr($i).'</span> </h3>';	
					$html .= '<p style="float:left; width:205px;"><a href="'.$permalink.'">'.$title.'</a></p>';
					}
			endif;
		endwhile;
        endif;	
		$html .='</div>';
	
  
        return $html;
});
	
	add_shortcode('gl_navigation', function() {
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
		
		$html .='<div style="clear:left;"></div>';
		return $html;

});
	

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

?>