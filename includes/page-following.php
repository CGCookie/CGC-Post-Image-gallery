<?php
/**
 * @package WordPress
 * @subpackage Default_Theme
 */
global $blog_id, $user_ID;

$user = urldecode($wp_query->query_vars['following']);
if(is_numeric($user)) {
	// if a user ID has been passed
	$user_data = get_userdata($user);
} else {
	// if a user name has been passed
	$user_data = get_user_by('login', $user);
}
$citizen = cgc_check_for_citizen(1, $user_data->ID);

get_header(); ?>

	<div class="content-wrap clearfix" id="primary">

		<?php get_template_part('notification', 'area'); ?>	
		
		<div id="main" class="main-content clearfix gallery no-sidebar">
			<div id="entries-wrap">
				<div id="entries" class="user-followers-wrap">			
					<div class="profile-section clearfix">
						<?php get_template_part('user', 'badges'); ?>
						<?php get_template_part('user', 'stats'); ?>
					</div>					
					<h2 class="title">
						<?php
							if($user_ID == $user_data->ID) {
								echo 'Users You are Following';	
							} else {
								echo 'Users followed by ' . $user_data->display_name;	
							}
						?>					
					</h2>
					
					<?php					
						$follower_ids = cgc_get_following($user_data->ID); 

						// pagination variables
						if (isset($wp_query->query_vars['p'])) $page = $wp_query->query_vars['p']; else $page = 1;
						$per_page = 36;
						$total_pages = 1;
						$offset = $per_page * ($page-1);
						$total_pages = ceil( count($follower_ids) /$per_page);							
												
						
						if($follower_ids) {	
							$get_followers_args = array(
								'include' => $follower_ids,
								'orderby' => 'registered',
								'order' => 'DESC',
								'number' => 36
							);
							$i = 1;
							$followers = get_users($get_followers_args);
							foreach($followers as $follower) {
								$class = '';
								if($i % 6 == 0) { $class = ' last'; }
								echo '<div class="follower' . $class . '">';
									echo '<a href="' . esc_url( home_url('/profile/' . urlencode( $follower->user_login ) ) ) . '" title="View ' . $follower->display_name . '\'s profile">';
										echo get_avatar($follower->user_email, 117);
										$name = $follower->display_name;
										if(strlen($name) > 14) { $name = substr($name, 0, 13) . '...'; } 
										echo '<div class="follower-name">' . $name . '</div>';
									echo '</a>';								
								echo '</div>';
								$i++;
							}
						} else {
							echo '<p class="no-followers">This user does not follow anyone.</p>';	
						}
						echo "<div id='wp_page_numbers' class='followers-pagination'>";
							$query_string = $_SERVER['QUERY_STRING'];
							$base = home_url('/followers/' . urlencode( $user_data->user_login ) ) . '/%_%';
							echo paginate_links( array(
								'base' => $base,
								'format' => '%#%',
								'prev_text' => __('previous'),
								'next_text' => __('next'),
								'total' => $total_pages,
								'current' => $page,
								'end_size' => 1,
								'mid_size' => 6,
							));				
						echo '</div>';
					?>					
					
				</div><!--end entries-->
			</div><!--end entries-wrap-->
			
			<?php get_sidebar('gallery'); ?>
			
		</div><!--end main-content-->
	
	</div><!--end content wrap-->

<?php get_footer(); ?>