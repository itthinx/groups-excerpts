<?php
/**
 * An example of how to show excerpts for posts protected by Groups.
 */
if (
		class_exists( 'Groups_Post_Access' ) &&
		method_exists( 'Groups_Post_Access', 'user_can_read_post' )
) {

	// remove the default filters
	remove_filter( 'posts_where', array( 'Groups_Post_Access', 'posts_where' ), 10 );
	remove_filter( 'get_the_excerpt', array( 'Groups_Post_Access', 'get_the_excerpt' ), 1 );
	remove_filter( 'the_content', array( 'Groups_Post_Access', 'the_content' ), 1 );

	// add a filter that will show the post content for authorized users and the
	// excerpt for those who aren't.
	add_filter( 'the_content', 'groups_excerpts_the_content', 1 );
	
	/**
	 * Content filter that shows the excerpt for unauthorized users.
	 * 
	 * @param string $output
	 * @return string
	 */
	function groups_excerpts_the_content( $output ) {
		global $post;
		$result = '';
		if ( isset( $post->ID ) ) {
			if ( Groups_Post_Access::user_can_read_post( $post->ID ) ) {
				$result = $output;
			} else {
	
				// show the excerpt
				$result .= '<div>';
				remove_filter( 'the_content', 'groups_excerpts_the_content', 1 );
				$result .= apply_filters( 'get_the_excerpt', $post->post_excerpt );
				add_filter( 'the_content', 'groups_excerpts_the_content', 1 );
				$result .= '</div>';
	
				// and add information to show that the content requires special access
				$result .= '<div>';
				$result .= '<p>';
				$result .= __( 'You need to become a premium member to access the full content.', 'groups-excerpts' );
				$result .= '</p>';
				$result .= '</div>';
			}
		} else {
			// not a post, don't interfere
			$result = $output;
		}
		return $result;
	}
}
