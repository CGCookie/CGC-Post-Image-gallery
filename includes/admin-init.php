<?php

add_action( 'admin_menu', 'pig_admin_menu' );

function pig_admin_menu(){
	add_media_page( __( 'Image Repair', 'pig' ), __( 'Image Repair', 'pig' ), 'delete_posts', 'pig-image-repair', 'pig_admin_image_repair' );
}

function pig_admin_image_repair(){

	$limit = 50;
	$current_page = isset( $_GET['paged'] ) ? (int)sanitize_text_field( $_GET['paged'] ) : 1;
	$offset = $current_page == 1 ? 0 : $limit * ( $current_page - 1 );

	$images = new WP_Query( array(
		'post_type' => 'images',
		'posts_per_page' => $limit,
		'offset' => $offset,
		'post_status' => 'publish',
		'orderby' => 'meta_value_num',
		'meta_key' => '_pig_image_404',
		'meta_query' => array(
			array(
				'key' => '_pig_image_404',
				'compare' => '!=',
				'value' => 'CGCOOKIE'
			)
		)
	) );

	$admin_url = admin_url( 'upload.php?page=pig-image-repair');

	$pages = ceil( $images->found_posts / $limit );
	$first = $admin_url;
	$last = add_query_arg( array( 'paged' => $pages ), $admin_url );
	$prev = $current_page <= 2 ? $first : add_query_arg( array( 'paged' => $current_page - 1 ), $admin_url );
	$next = $current_page >= $pages - 1 ? $last : add_query_arg( array( 'paged' => $current_page + 1 ), $admin_url );

	$pagination = '<div class="tablenav-pages">';
		$pagination .= '<span class="displaying-num">' . $images->post_count . ' items</span>';
		if( $pages > 1){
			$pagination .= '<span class="pagination-links">';
				$pagination .= '<a class="first-page' . ($current_page == 1 ? ' disabled' : '') . '" title="Go to the first page" href="' . $first . '">&laquo;</a>';
				$pagination .= '<a class="prev-page' . ($current_page == 1 ? ' disabled' : '') . '" title="Go to the previous page" href="' . $prev . '">&lsaquo;</a>';
				$pagination .= ' <span class="paging-input">' . $current_page . ' of <span class="total-pages">' . $pages . '</span></span> ';
				$pagination .= '<a class="next-page' . ($current_page == $pages ? ' disabled' : '') . '" title="Go to the next page" href="' . $next . '">&rsaquo;</a>';
				$pagination .= '<a class="last-page' . ($current_page == $pages ? ' disabled' : '') . '" title="Go to the last page" href="' . $last . '">&raquo;</a>';
			$pagination .= '</span>';
		}
	$pagination .= '</div>';
	$pagination .= '<br class="clear">';

	include( 'admin-page.php' );
}

add_action( 'wp', 'pig_schedule_daily_prune' );

function pig_schedule_daily_prune() {
	if ( ! wp_next_scheduled( 'pig_daily_prune' ) )
		wp_schedule_event( strtotime( '3:00 am' ), 'daily', 'pig_daily_prune' );
}

add_action( 'pig_daily_prune', 'pig_remove_broken_images' );

function pig_remove_broken_images() {
	$current_time = current_time( 'timestamp' );
	$images = new WP_Query( array(
		'post_type' => 'images',
		'posts_per_page' => $limit,
		'offset' => $offset,
		'post_status' => 'publish',
		'meta_query' => array(
			array(
				'key' => '_pig_image_404',
				'compare' => '<=',
				'value' => $current_time
			)
		)
	) );

	while( $images->have_posts() ): $images->the_post();
		// double check to make sure the meta value is present and fits the requirements
		$expire_time = get_post_meta( get_the_ID(), '_pig_image_404', true );

		if( $expire_time && $expire_time <= $current_time ){
			wp_delete_post( get_the_ID(), true );
		}

	endwhile;
	wp_reset_postdata();
}