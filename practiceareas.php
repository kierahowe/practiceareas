<?php
/**
 * @package PracticeAreas
 * @version 1.0
 */
/*
Plugin Name: Practice Areas
Author: Kiera Howe
Version: 1.0
Author URI: http://www.kierahowe.com/
*/

class practiceareas extends WP_Widget {

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {
		$widget_ops = array( 
			'classname' => 'practiceareas',
			'description' => 'Practice Area',
		);
		parent::__construct( 'practiceareas', 'Practice Area', $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		// outputs the content of the widget
		
		echo $args['before_widget'];
		?>
		<div class="practice" >
			<div class="practicepic" style="background-image: url(<?php echo wp_get_attachment_image_src($instance['thumbnail'])[0]; ?>);">
			</div>
			<div class="practicetitle">
				<?php 
					if ( ! empty( $instance['title'] ) ) {
						if (substr($instance['pagelink'], 0, 7) == "http://" || substr($instance['pagelink'], 0, 8) == "https://") { 
							$x = "";
						} else { 
							$x = site_url(). "/";						
						}
						echo $args['before_title'] . 
							"<a href=\"" . $x .$instance['pagelink'] . "\">" . 
								apply_filters( 'widget_title', $instance['title'] ) . 
							"</a>" . 
							$args['after_title'];
					}
				?>
			</div>
			<div class="practicepreview">
				<?php 
					$args = array ("post_type" => "post", "posts_per_page" => 1, "cat" => $instance['category']);
					$the_query = new WP_Query( $args );

					$out = array ();
					if ( $the_query->have_posts() ) {
						$the_query->the_post();
						print "<h4 style=\"height: 50px;\"><a href=\"" . get_permalink() . "\">" . get_the_title() . "</a></h4>";
						//the_excerpt ();
						echo "<div class=\"practiceexcerpt\">" . practice_excerpt(24) . "</div>";
						print "<p><a href=\"" . get_permalink() . "\"><i class=\"fa fa-circle\" aria-hidden=\"true\"></i><i class=\"fa fa-circle\" aria-hidden=\"true\"></i><i class=\"fa fa-circle\" aria-hidden=\"true\"></i></a></p>";
					}

					wp_reset_postdata();
				?>
			</div>
		</div>
 <?php 
		echo $args['after_widget'];
		
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		// outputs the options form on admin
		$category = ! empty( $instance['category'] ) ? $instance['category'] : __( '', 'text_domain' );
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'text_domain' );
		$thumbnail = ! empty( $instance['thumbnail'] ) ? $instance['thumbnail'] : __( '', 'text_domain' );
		$pagelink = ! empty( $instance['pagelink'] ) ? $instance['pagelink'] : __( '', 'text_domain' );

		$args = array( 'taxonomy' => 'category' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( esc_attr( 'Title:' ) ); ?></label> 
		<input type=text class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" 
			value="<?php echo esc_attr( $title ); ?>">
		</p>

		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'category' ) ); ?>"><?php _e( esc_attr( 'Category:' ) ); ?></label> 
			<?php 
			
			$args = array (
				"show_count" => '1',
				"hierarchical" => '1',
				"hide_empty" => '0',
				"id" => esc_attr( $this->get_field_id( 'category' ) ), 
				"name" => esc_attr( $this->get_field_name( 'category' ) ),
				"selected" => $category
				); 
			
			wp_dropdown_categories($args); ?>
		</p>

		<div class="uploader">
			<input id="<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>" 
				name="<?php echo esc_attr( $this->get_field_name( 'thumbnail' )); ?>" type="text" value="<?php echo $thumbnail; ?>" />
			<input id="<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>_button" 
						class="button" 
						type="text" value="Upload" />
		</div>
		
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'pagelink' ) ); ?>"><?php _e( esc_attr( 'Pagelink:' ) ); ?></label> 
		<input type=text class="widefat" 
			id="<?php echo esc_attr( $this->get_field_id( 'pagelink' ) ); ?>" 
			name="<?php echo esc_attr( $this->get_field_name( 'pagelink' ) ); ?>" 
			value="<?php echo esc_attr( $pagelink ); ?>">
		</p>
	<script>


	jQuery(document).ready(function($){
		var _custom_media = true,
		_orig_send_attachment = wp.media.editor.send.attachment;

		$('.uploader #<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>_button').click(function(e) {
			var send_attachment_bkp = wp.media.editor.send.attachment;
			
			_custom_media = true;
			wp.media.editor.send.attachment = function(props, attachment){
				console.log (attachment.id);
				document.getElementById ('<?php echo esc_attr( $this->get_field_id( 'thumbnail' ) ); ?>').value = 
					attachment.id;
			}

			wp.media.editor.open();
			return false;
		});

		$('.add_media').on('click', function(){
			_custom_media = false;
		});
	});
	</script>
		<?php 
		
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['category'] = ( ! empty( $new_instance['category'] ) ) ? strip_tags( $new_instance['category'] ) : '';
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['thumbnail'] = ( ! empty( $new_instance['thumbnail'] ) ) ? strip_tags( $new_instance['thumbnail'] ) : '';
		$instance['pagelink'] = ( ! empty( $new_instance['pagelink'] ) ) ? strip_tags( $new_instance['pagelink'] ) : '';
		return $instance;
	}
}

add_action( 'widgets_init', function(){
	register_widget( 'practiceareas' );
});

function practice_newsarticles() {
	$labels = array(
		'name'                  => _x( 'News Articles', 'News Article General Name', 'text_domain' ),
		'singular_name'         => _x( 'News Article', 'News Article Singular Name', 'text_domain' ),
		'menu_name'             => __( 'News Articles', 'text_domain' ),
		'name_admin_bar'        => __( 'News Article', 'text_domain' ),
		'archives'              => __( 'Item Archives', 'text_domain' ),
		'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
		'all_items'             => __( 'News Articles', 'text_domain' ),
		'add_new_item'          => __( 'Add New Item', 'text_domain' ),
		'add_new'               => __( 'Add New', 'text_domain' ),
		'new_item'              => __( 'New Item', 'text_domain' ),
		'edit_item'             => __( 'Edit Item', 'text_domain' ),
		'update_item'           => __( 'Update Item', 'text_domain' ),
		'view_item'             => __( 'View Item', 'text_domain' ),
		'search_items'          => __( 'Search Item', 'text_domain' ),
		'not_found'             => __( 'Not found', 'text_domain' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
		'featured_image'        => __( 'Featured Image', 'text_domain' ),
		'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
		'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
		'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
		'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
		'items_list'            => __( 'Items list', 'text_domain' ),
		'items_list_navigation' => __( 'Items list navigation', 'text_domain' ),
		'filter_items_list'     => __( 'Filter items list', 'text_domain' ),
	);
	$args = array(
		'label'                 => __( 'News Article', 'text_domain' ),
		'description'           => __( 'News Article Description', 'text_domain' ),
		'labels'                => $labels,
		'supports'              => array('title', 'editor',  'thumbnail'),
		'taxonomies'            => array( ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 80,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,		
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'newsarticles', $args );
}
add_action( 'init', 'practice_newsarticles', 0 );


function practice_excerpt($limit) {
  $excerpt = explode(' ', get_the_excerpt(), $limit);

  if (count($excerpt)>=$limit) {
    array_pop($excerpt);
    $excerpt = implode(" ",$excerpt).'...';
  } else {
    $excerpt = implode(" ",$excerpt);
  }	
  $excerpt = preg_replace('`[[^]]*]`','',$excerpt);
  return $excerpt;
}
