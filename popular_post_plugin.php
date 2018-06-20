<?php
/**
* Plugin Name: Popular Posts Plugin By Neha
* Description: A brief plugin to evaluate the popularity of blog posts on the admin dashboard
* Version: 0.1
* Author: Neha Bisht
* Author URI: http://lynda.com/mor10
* License: GPL2
*/

function my_popular_post_views($postID){
    $total_key = 'views';
    $total = get_post_meta( $postID, $total_key, true );
    if( $total == '' ){
        delete_post_meta( $postID, $total_key );
        add_post_meta( $postID, $total_key, '0' );
    }
    else{
        $total++;
        update_post_meta( $postID, $total_key, $total );
    }
}

function my_count_popular_posts($post_id){
    if( !is_single() ) return;
    if( !is_user_logged_in() ){
        if( empty ( $post_id ) ){
            global $post;
            $post_id = $post->ID;
        }
        my_popular_post_views($post_id);
    }
}
add_action( 'wp_head', 'my_count_popular_posts' );

function my_add_views_column($defaults){
    $defaults['post_views'] = __('View Count');
    return $defaults; 
}

function my_display_views($column_name){
    if($column_name === 'post_views'){
        echo (int) get_post_meta(get_the_ID(), 'views', true);
    }
}

add_filter('manage_posts_columns', 'my_add_views_column');
add_action('manage_posts_custom_column', 'my_display_views',5,2);


class popular_posts extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'popular_posts', // Base ID
			esc_html__( 'Popular Posts', 'text_domain' ), // Name
			array( 'description' => esc_html__( 'The two most popular posts', 'text_domain' ), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}
		//Query
        //Custom Query
    

// The Query
$query_args = array(
        'post_type' => 'post',
            'posts_per_page' => 5,
            'meta_keys' => 'views',
            'orderby' => 'meta_value_num',
            'order' => 'DESC',
            'ignore_sticky_posts' => true
            
        
        
        );
            
            $the_query = new WP_Query( $query_args );

// The Loop
             if ( $the_query->have_posts() ) {
	           echo '<ul>';
	         while ( $the_query->have_posts() ) {
		    $the_query->the_post();
		     echo '<li>' ;
                 echo '<a href="' . get_the_permalink() . '" rel=bookmark">';
                 echo  get_the_title();
                 echo ' (' .get_post_meta( get_the_ID(),'views', true ) . ')';
                 echo '</a>';
                 echo '</li>';
	           }
	                echo '</ul>';
	         /* Restore original Post Data */
	       wp_reset_postdata();
} else {
	// no posts found
}
		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'New title', 'text_domain' );
		?>
		<p>
		<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', 'text_domain' ); ?></label> 
		<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';

		return $instance;
	}

} // class Foo_Widget


// register Foo_Widget widget
function register_popular_posts() {
    register_widget( 'popular_posts' );
}
add_action( 'widgets_init', 'register_popular_posts' );