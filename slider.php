<?php 
/**
 * Main script for registering the custom post type for a slider and the metaboxes
 * The display of this slider is regulated by views/slider-template.php
 * 
 * @author Michiel
 */

/**
 * Registers the custom post type for the slider
 *
 * @uses register_post_type Function to register a new post type
 */
function create_post_type_slider_feature() {
	register_post_type( 'slider_feature',
		array(
		'labels' => array(
                    'name' => _x( 'Slider Items', 'post type general name', 'language_domain'),
                    'menu_name' => __( 'Slider Items', 'language_domain' ),
                    'singular_name' => _x('Slider Item', 'post type singular name', 'language_domain' ),
                    'add_new' => _x('Add New', 'slider_feature', 'language_domain'),
                    'add_new_item' => __('Add New Slider Item', 'language_domain'),
                    'edit_item' => __('Edit Slider Item', 'language_domain'),
                    'new_item' => __('New Slider Item', 'language_domain'),
                    'all_items' =>t __('All Slider Items', 'language_domain'),
                    'view_item' => __('View Slider Item', 'language_domain'),
                    'search_items' => __('Search Slider Items', 'language_domain'),
                    'not_found' => __('No Slider Items found', 'language_domain'),
                    'not_found_in_trash' => __('No Slider Items found in Trash', 'language_domain'),
                    ),
		'public' =>; true,
		'has_archive' =>; false,
		'supports' =>; array('custom-fields','title', 'editor'),
		)
	);
}

add_action( 'init', 'create_post_type_slider_feature' );


/**
 * Returns the slider custom fields, so we avoid using a global variable
 */
function get_slider_custom_fields() {
    
    $slider_custom_fields = array(
        "feature_link" => array(
            "name" => 'slider_link',
            "std" => '',
            "title" => __('Post or page to link the feature slider item to:', 'language_domain')
        ),
        "image" => array(
            "name" => 'slider_image',
            "std" => '',
            "title" => __('Link to feature image:', 'language_domain');
        ),
    ); 
    
    return $slider_custom_fields;
}

/**
 * Registers a callback for displaying the slider custom fields
 * These custom fields are displayed at the post type you add this callback to.
 *
 * @var array $slider_custom_fields The array with the custom fields used
 */
function slider_custom_fields() {
	
    global $post;  
    
    $slider_custom_fields = get_slider_custom_fields();
    
	foreach($slider_custom_fields as $meta_box) {
        
        /*  Gets the meta box value for each meta box */
		$meta_box_value = stripslashes( get_post_meta($post->ID, $meta_box['name'].'_value', true) );
		
        /* Revert to the default value if there is no value saved yet */
        if($meta_box_value == "")
			$meta_box_value = $meta_box['std'];
        
        echo '<p style="margin-bottom:10px;">';
        echo'<input type="hidden" name="'.$meta_box['name'].'_noncename" id="'.$meta_box['name'].'_noncename" value="'.wp_create_nonce( plugin_basename(__FILE__) ).'" />';
        echo'<strong>'.$meta_box['title'].'</strong>';
        echo'<input type="text" name="'.$meta_box['name'].'_value" value="'.attribute_escape($meta_box_value).'" style="width:100%;" /><br />';
        echo '</p>';
    }
}

/**
 * Adds the metabox, define the title and location for the metaboxes.
 * Secondly, determine to which post type (in this case slider_feature) the metaboxes should be placed
 *
 * @uses add_meta_box Function to register a metabox
 */
function create_meta_box() {
    if ( function_exists('add_meta_box') ) {
		add_meta_box( 'new-meta-boxes', 'Slider Settings', 'slider_custom_fields', 'slider_feature', 'normal', 'high' );
	}
}

/**
 * Saves the data of the metaboxes after checking the user and nonces. 
 * This script should be in the same file as your slider_custom_fields function,
 * as it is used to create a nonce
 *
 * @param int $post_id The post-id we're saving for.
 */
function save_sliderdata( $post_id ) {
    
	global $post;
    
    $slider_custom_fields = get_slider_custom_fields();
    
    // Loop through each save action
	foreach($slider_custom_fields as $meta_box) {
        
        // Verify that the nonce is valid
        if ( !wp_verify_nonce( $_POST[$meta_box['name'].'_noncename'], plugin_basename(__FILE__) )) {
            return $post_id;
        }
        
        // Verify that a user can actually edit the post
        if ( 'page' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_page', $post_id ))
                return $post_id;
        } else {
			if ( ! current_user_can( 'edit_post', $post_id ))
                return $post_id;
        }
        
        // Now continue with saving the data.
		$data = $_POST[$meta_box['name'].'_value'];
        
		// If value is non existent add it as post meta data
        if(get_post_meta($post_id, $meta_box['name'].'_value') == "")
		  add_post_meta($post_id, $meta_box['name'].'_value', $data, true);
        
        // If it is existing, update
		elseif($data != get_post_meta($post_id, $meta_box['name'].'_value', true))
		  update_post_meta($post_id, $meta_box['name'].'_value', $data);
		
        // Else, delete if data is empty
        elseif($data == "")
		  delete_post_meta($post_id, $meta_box['name'].'_value', get_post_meta($post_id, $meta_box['name'].'_value', true));
	}
}

/** 
 * Register the metaboxes and save actions
 */
add_action('admin_menu', 'create_meta_box');
add_action('save_post', 'save_sliderdata');

/**
 * Register any scripts to be used by the slider
 * We register these scripts using wp_enqueue_script just before the body tag.
 */
function slider_register_scripts() {
    
    // wp_enqueue_script('jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js', array(), '1.6.4', true); Most likely, this is already registered at your WordPress Installation
    wp_enqueue_script('SlidesJS', get_template_directory_uri() . '/assets/js/slides.min.jquery.js', array('jquery'), false, true);
    wp_enqueue_script('Custom-JS', get_template_directory_uri() . '/assets/js/custom.js', array('jquery'), false, true);
}
add_action('wp_enqueue_scripts', 'slider_register_scripts');
