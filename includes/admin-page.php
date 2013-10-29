<div class="wrap">
	<h2><?php _e( 'Image Repair', 'pig' ); ?></h2>
	<?php if( $images->have_posts() ): ?>
		<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
			<table class="widefat">
				<thead>
					<tr>
						<th>Post ID</th>
						<th>Post Title</th>
						<th>Purge Date</th>
						<th colspan="2" class="actions">Actions</th>
					</tr>
				</thead>
				<tbody>
					<?php while( $images->have_posts() ): $images->the_post();
						$expires = get_post_meta( get_the_ID(), '_pig_image_404', true ); ?>
						<tr>
							<td><?php the_ID(); ?>
							<td><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></td>
							<td><?php echo date( 'n/j/Y g:i a', $expires ); ?></td>
							<td><a href="<?php echo get_edit_post_link( get_the_ID() ); ?>">Edit</a></td>
							<td><a href="<?php echo get_delete_post_link( get_the_ID() ); ?>">Delete</a></td>
						</tr>
					<?php endwhile; wp_reset_postdata(); ?>
				</tbody>
			</table>
		</form>
	<?php else: ?>
		<p><?php _e( 'No images in need of repair.', 'pig' ); ?>
	<?php endif; ?>
</div>