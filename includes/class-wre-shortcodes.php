<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class WRE_Shortcodes {

	public function __construct() {
		add_filter( 'wp', array( $this, 'has_shortcode' ) );
		add_shortcode( 'wre_listing', array( $this, 'listing' ) );
		add_shortcode( 'wre_listings', array( $this, 'listings' ) );
		add_shortcode( 'wre_agent', array( $this, 'agent' ) );
		add_shortcode( 'wre_nearby_listings', array( $this, 'nearby_listings' ) );
		add_shortcode( 'wre_agents', array( $this, 'wre_agents' ) );
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
			global $post;
			if ( is_a( $post, 'WP_Post' ) &&
				( has_shortcode( $post->post_content, 'wre_listing') || 
				has_shortcode( $post->post_content, 'wre_listings') || 
				has_shortcode( $post->post_content, 'wre_agent') ||
				has_shortcode( $post->post_content, 'wre_search' ) ||
				has_shortcode( $post->post_content, 'wre_contact_form' ) ||
				has_shortcode( $post->post_content, 'wre_nearby_listings' ) ||
				has_shortcode( $post->post_content, 'wre_agents' ) )
			)
			{
				add_filter( 'is_wre', array( $this, 'return_true' ) );
			}

			if ( is_a( $post, 'WP_Post' ) && 
				has_shortcode( $post->post_content, 'wre_listing') )
			{
				add_filter( 'is_single_wre', array( $this, 'return_true' ) );
			}
	}

	/**
	 * Add this as a wre page
	 *
	 * @param bool $return
	 * @return bool
	 */
	public function return_true( $return ) {
		return true;
	}

	/**
	 * Loop over found listings.
	 * @param  array $query_args
	 * @param  array $atts
	 * @param  string $loop_name
	 * @return string
	 */
	private static function listing_loop( $query_args, $atts, $loop_name ) {

		$listings = new WP_Query( apply_filters( 'wre_shortcode_query', $query_args, $atts, $loop_name ) );

		ob_start();

			if ( $listings->have_posts() ) { ?>

				<?php do_action( "wre_shortcode_before_{$loop_name}_loop" ); ?>

					<ul class="wre-items">

						<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

								<?php wre_get_part( 'content-listing.php' ); ?>

						<?php endwhile; // end of the loop. ?>

					</ul>

				<?php

				do_action( "wre_shortcode_after_{$loop_name}_loop" );

			} else {
				do_action( "wre_shortcode_{$loop_name}_loop_no_results" );
			}

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * List multiple listings shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function listings( $atts ) {
		$atts = shortcode_atts( array(
			'orderby'	=> 'date',
			'order'		=> 'asc',
			'number'	=> '20',
			'agent'		=> '', // id of the agent
			'ids'		=> '',
			'compact'	=> '',
		), $atts );

		$query_args = array(
			'post_type'				=> 'listing',
			'post_status'			=> 'publish',
			'ignore_sticky_posts'	=> 1,
			'orderby'				=> $atts['orderby'],
			'order'					=> $atts['order'],
			'posts_per_page'		=> $atts['number'],
		);

		if ($atts['orderby'] == 'price') {
			$query_args['meta_key'] = '_wre_listing_price';
			$query_args['orderby'] = 'meta_value_num';
		}

		if ( ! empty( $atts['ids'] ) ) {
			$query_args['post__in'] = array_map( 'trim', explode( ',', $atts['ids'] ) );
		}

		if ( ! empty( $atts['agent'] ) ) {
			$query_args['meta_key']     = '_wre_listing_agent';
			$query_args['meta_value']   = absint( $atts['agent'] );
			$query_args['meta_compare'] = '=';
		}

		// if we are in compact mode
		if ( ! empty( $atts['compact'] ) && $atts['compact'] == 'true' ) {
			remove_action( 'wre_before_listings_loop_item_wrapper', 'wre_before_listings_loop_item_wrapper', 10 );
			remove_action( 'wre_after_listings_loop_item_wrapper', 'wre_after_listings_loop_item_wrapper', 10 );
			
			remove_action( 'wre_listings_loop_item', 'wre_template_loop_at_a_glance', 40 );
			remove_action( 'wre_listings_loop_item', 'wre_template_loop_description', 50 );
			remove_action( 'wre_listings_loop_item', 'wre_template_loop_compare', 60 );
			add_filter( 'post_class', array( __CLASS__, 'listings_compact_mode' ), 20, 3 );
		}

		return self::listing_loop( $query_args, $atts, 'listings' );
	}

	/**
	 * List nearby listings shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function nearby_listings( $atts ) {
		$atts = shortcode_atts( array(
			'distance'	=> 'miles',
			'radius'	=> '50',
			'number'	=> '3',
			'compact'	=> 'true',
			'view'		=> 'list-view',
			'columns'	=> '3'
		), $atts );

		$key = wre_map_key();
		if( ! $key ) return;

		ob_start();
		?>
			<div class="nearby-listings-wrapper" data-listing-view="<?php echo esc_attr( $atts['view'] ); ?>" data-columns="<?php echo esc_attr( $atts['columns'] ); ?>" data-distance="<?php echo esc_attr( $atts['distance'] ); ?>" data-radius="<?php echo esc_attr( $atts['radius'] ); ?>" data-number="<?php echo esc_attr( $atts['number'] ); ?>" data-compact="<?php echo esc_attr( $atts['compact'] ); ?>">
				<img src="<?php echo WRE_PLUGIN_URL ?>/assets/images/loading.svg" />
			</div>
		<?php
		return ob_get_clean();
	}

	/**
	 * List Agents shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function wre_agents( $atts ) {
		
		$atts = shortcode_atts( array(
			'view'				=> 'lists',
			'number'			=> wre_option('agents_archive_max_agents') ? wre_option('agents_archive_max_agents') : 10,
			'allow_pagination'	=> wre_option('agents_archive_allow_pagination') ? wre_option('agents_archive_allow_pagination') : 'yes',
			'items'				=> '2',
			'autoplay'			=> true,
			'effect'			=> 'slide',
			'dots'				=> true,
			'agents-view'		=> wre_option('wre_agents_mode') ? wre_option('wre_agents_mode') : 'list-view',
			'agent-columns'		=> wre_option('wre_archive_agents_columns') ? wre_option('wre_archive_agents_columns') : 2,
			'controls'			=> true,
			'loop'				=> true,
		), $atts );

		$paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;

		$agent_details = array();
		
		$agents_args = array(
			'role__in'	=> array( 'wre_agent', 'administrator' ),
			'number'	=> -1,
			'fields'	=> array( 'ID', 'user_email', 'display_name', 'user_url' )
		);
		
		$agents = get_users( $agents_args );

		foreach( $agents as $agent ) {

			if( user_can( $agent->ID, 'wre_agent' )) {
				$agent_details[] = $agent;
			} else if( count_user_posts( $agent->ID, 'listing' ) > 0 ) {
				$agent_details[] = $agent;
		}
		}
		
		$number = $atts['number'];

		if( $atts['allow_pagination'] == 'yes' && $atts['view'] == 'lists' && !empty( $agent_details ) ) {
			$total_agents = count( $agent_details );
			$agent_details = array_chunk($agent_details, $number);
			$agent_details = $agent_details[$paged-1];
		}

		if( $atts['view'] == 'carousel' && !empty( $agent_details ) ) {
			$agent_details = array_slice( $agent_details, 0, $number );
		}

		if( ! empty( $agent_details ) ) {

		$attributes = '';
		if( $atts['view'] == 'carousel' ) {
			$attributes = json_encode( $atts, true );
		}

		ob_start();
		?>
			<div class="wre-agents-container">
				<div class="agents-wrapper agents-<?php echo esc_attr( $atts['view'] ).' '.esc_attr( $atts['agents-view'] ); ?>" data-value='<?php echo $attributes; ?>'>
					<?php
					foreach( $agent_details as $user ) {
						$agent_id	= $user->ID;
						$phone		= get_the_author_meta( 'phone', $agent_id );
						$mobile		= get_the_author_meta( 'mobile', $agent_id );
						$email		= $user->user_email;
						$website	= $user->user_url;
						$agent_name = $user->display_name;
						$gravatar_url = wre_get_agent_attachment_url($agent_id);
						$position = get_the_author_meta( 'title_position', $agent_id );
						$agent_description = strip_tags( get_the_author_meta( 'description', $agent_id ) );
						$agent_description = wp_trim_words( $agent_description, 20 );
						$agent_url = get_author_posts_url( $agent_id );

						?>
							<div class="wre-col col-<?php echo esc_attr( $atts['agent-columns'] ); ?>">

								<div class="agent-inner-wrap">

									<div class="agent-image-wrapper wre-social-icons-wrapper">
										<div class="avatar-wrap">
											<a href="<?php echo esc_url( $agent_url ); ?>">
												<img src="<?php echo esc_url( $gravatar_url ); ?>" />
											</a>
										</div>
										<?php wre_get_agent_social_share_data( $agent_id ); ?>
									</div>

									<div class="wre-agent-details content">

										<h4 class="name">
											<a href="<?php echo esc_url( $agent_url ); ?>">
												<?php echo esc_html( $agent_name ); ?>
											</a>
										</h4>

										<?php if( $position ) { ?>
											<p class="position"><?php echo esc_html( $position ); ?></p>
										<?php } ?>

										<?php wre_get_agent_contact_details( $agent_id ); ?>

										<?php if( $agent_description && $atts['view'] == 'lists' ) { ?>
											<div class="description"><?php echo esc_html( $agent_description ); ?></div>
										<?php } ?>

									</div>

								</div>

							</div>
					<?php } ?>
				</div>
				<?php if( $atts['allow_pagination'] == 'yes' && $atts['view'] == 'lists' ) { ?>
					<nav class="wre-pagination">
						<?php
							$pl_args = array(
								'base'		=>	add_query_arg('paged','%#%'),
								'format'	=>	'',
								'total'		=>	ceil( $total_agents / $atts['number'] ),
								'current'	=>	max( 1, $paged ),
								'prev_text'	=>	'&larr;',
								'next_text'	=>	'&rarr;',
								'type'		=>	'list',
								'end_size'	=>	3,
							);
							echo paginate_links($pl_args);
						?>
					</nav>
				<?php } ?>
			</div>

		<?php
		} else {
			echo '<p>'.__( 'No agent found.','wp-review' ).'</p>';
		}
		if( $atts['view'] == 'carousel' ) {
			wp_enqueue_style('wp-real-estate-lightslider');
			wp_enqueue_script('wp-real-estate-lightslider');
		}

		return ob_get_clean();
	}

	/**
	 * Add the compact class to the listings
	 */
	public static function listings_compact_mode( $classes, $class = '', $post_id = '' ) {
		$classes[] = 'compact';
		return $classes;
	}

	/**
	 * Display a single listing.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function listing( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		$args = array(
			'post_type'			=> 'listing',
			'posts_per_page'	=> 1,
			'no_found_rows'		=> 1,
			'post_status'		=> 'publish',
		);

		if ( isset( $atts['id'] ) ) {
			$args['p'] = $atts['id'];
		}

		ob_start();

			$listings = new WP_Query( apply_filters( 'wre_shortcode_query', $args, $atts ) );

			if ( $listings->have_posts() ) : ?>

				<div id="listing-<?php the_ID(); ?>" class="wre-single">

					<?php while ( $listings->have_posts() ) : $listings->the_post(); ?>

						<?php wre_get_part( 'content-single-listing.php' ); ?>

					<?php endwhile; // end of the loop. ?>

				</div>

			<?php endif;

			wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Display a single agent.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function agent( $atts ) {
		if ( empty( $atts ) ) {
			return '';
		}

		return wre_get_single_agent_data( $atts['id'] );
	}

}

return new WRE_Shortcodes();