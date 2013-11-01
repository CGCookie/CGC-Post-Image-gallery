<?php

function pig_user_dashboard_images() {
	global $current_user;
	ob_start();

	// get all network sites
	$network_sites = get_blogs_of_user(1, false);

	$image_args = array(
		'author' => $current_user->ID,
		'post_type' => 'images',
		'posts_per_page' => -1
	);

	foreach( $network_sites as $site ) :

		if( $site->userblog_id == 1 )
			continue;

		switch_to_blog( $site->userblog_id );

		// The Query
		$the_query = new WP_Query($image_args);

		echo '<div class="site-portfolio">';
		if( $the_query->have_posts() ) :
			echo '<h5 class="site-portfolio-name">' . $site->blogname . ' Images</h5>';
			echo '<span class="site-gallery-controls">';
				echo '<a href="' . network_home_url($site->path . 'profile/' . $current_user->user_login) . '"><i class="icon-eye-open"></i> view gallery</a>';
				echo '<a href="'. $site->siteurl .'/submit-image" title="Submit a new image"><i class="icon-plus"></i> Add Image</a>';
			echo '</span>';
			echo '<div id="user-portfolio-images" class="gallery clearfix">';
			// The Loop
			while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
				<div class="pig-grid-image">
					<a href="<?php the_permalink(); ?>" title="View this image">
					<!--<a href="<?php echo get_post_meta(get_the_ID(), 'pig_image_url', true); ?>" title="View this image">-->
						<?php if( get_the_post_thumbnail( get_the_ID(), 'medium-thumb') ) { ?>
							<?php the_post_thumbnail('medium-thumb'); ?>
						<?php } ?>
					</a>
					<div class="edit-image-inline">	
						<ul class="gallery-image-controls">
							<li id="<?php echo get_the_ID(); ?>" class="edit-image">
								<a class="image-edit-modal-toggle" href="#" data-reveal-id="image-edit-modal" title="Edit this Image"><i class="icon-pencil"></i></a>
								<div class="image-mature hidden"><?php if(get_post_meta(get_the_ID(), 'pig_mature', true)) { echo 'yes'; } else { echo 'no'; } ?></div>
								<div class="image-subsite-id hidden"><?php echo get_post_meta(get_the_ID(), 'pig_subsite_id', true); ?></div>
								<div class="image-title hidden"><?php echo get_the_title(); ?></div>
								<div class="image-description hidden"><?php echo htmlentities(get_the_content()); ?></div>
								<div class="image-id hidden"><?php echo get_the_ID(); ?></div>
							</li>
							<li id="remove-<?php echo get_the_ID(); ?>" class="delete-image">
								<a class="image-delete-modal-toggle" href="#image-delete-modal" data-reveal-id="image-delete-modal" name="image-delete-modal" title="Delete this Image"><i class="icon-trash"></i></a>
								<div class="image-subsite-id hidden"><?php echo get_post_meta(get_the_ID(), 'pig_subsite_id', true); ?></div>
								<div class="image-id hidden"><?php echo get_the_ID(); ?></div>
							</li>
						</ul>
					</div>
				</div>

			<?php
			endwhile;
			echo '</div><!-- ends #user-portfolio-images -->'; // ends #user-portfolio-images

			// Reset Post Data
			wp_reset_postdata();

		else :

			echo '<h5 class="site-portfolio-name">' . $site->blogname . ' Images</h5>';
			echo '<span class="site-gallery-controls">';
				echo '<a href="'. $site->siteurl .'/submit-image" title="Submit a new image"><i class="icon-plus"></i> Add Image</a>';
			echo '</span>';
			echo '<p class="empty">You have no images uploaded to '. $site->blogname .'</p>';
		endif;

		echo '</div><!-- ends .site-portfolio -->'; // ends .site-portfolio

		restore_current_blog();

	endforeach; // foreach sites

	return ob_get_clean();
}

function pig_user_image_count() {
	global $current_user;
	ob_start();
	$image_count = 0;
	// get all network sites
	$network_sites = get_blogs_of_user(1, false);

	$image_args = array(
		'author' => $current_user->ID,
		'post_type' => 'images',
		'posts_per_page' => -1
	);

	foreach( $network_sites as $site ) :

		if( $site->userblog_id == 1 )
			continue;

		switch_to_blog( $site->userblog_id );

		// The Query
		$the_query = new WP_Query($image_args);

		if( $the_query->have_posts() ) {
			$image_count = $the_query->post_count;
		}
	endforeach;

		return $image_count;
}

function pig_image_edit_form() {
	ob_start(); ?>

	<form id="pig-image-edit" action="" method="POST"/>

		<input type="hidden" name="pig-image-action" value="edit" />
		<input type="hidden" id="pig-image-id" name="pig-image-id" value="" />
		<input type="hidden" id="pig-subsite-id" name="pig-subsite-id" value="" />
		<input type="hidden" id="pig-referrer" name="pig-referrer" value="<?php the_permalink(); ?>" />
		<input type="hidden" id="pig-redirect-to" name="pig-redirect-to" value="" />
		<p>
			<label for="pig-image-title">Edit your image title</label>
			<input type="text" id="pig-image-title" name="pig-image-title" value="" />
		</p>
		<p><label for="pig-image-desc">Edit your image description</label>
		<textarea id="pig-image-desc" name="pig-image-desc" rows="8"></textarea>
		</p>
		<p>
			<label for="pig-image-mature" class="checkbox">
				<input type="checkbox" id="pig-image-mature" name="pig-image-mature" value="1" />
				Contains mature content?
			</label>
		</p>
		<input type="submit" class="button" id="pig_submit" value="Submit Update" />
		<a href="#" class="close cancel" id="pig_cancel"><i class="icon-remove"></i> Cancel</a>
	</form>

	<?php
	return ob_get_clean();
}

function pig_image_remove_form() {
	ob_start(); ?>
	<div id="pig-image-delete-wrap">
		<h3 class="reveal-modal-header">Are you sure you want to delete?</h3>
		<p><strong>Clicking yes will obliterate the data beyond recovery</strong></p>
		<form id="pig-image-remove" action="" method="POST"/>

			<input type="hidden" name="pig-image-action" value="delete" />
			<input type="hidden" id="pig-delete-image-id" name="pig-image-id" value="" />
			<input type="hidden" id="pig-delete-subsite-id" name="pig-subsite-id" value="" />
			<input type="hidden" id="pig-delete-referrer" name="pig-referrer" value="<?php the_permalink(); ?>" />
			<input type="hidden" id="pig-delete-redirect-to" name="pig-delete-redirect-to" value="" />
			<input type="submit" id="pig_remove_image" value="Delete Image Forever" />
			<a href="#" id="pig-image-delete-cancel" class="close cancel"><i class="icon-remove"></i> Wait, no!</a>
		</form>
	</div><!--end #pig-image-delete-wrap-->
	<?php
	return ob_get_clean();
}
