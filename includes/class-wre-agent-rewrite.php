<?php
if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

class WRE_Agent_Rewrite {

	public function __construct() {
		add_filter('wp', array($this, 'has_shortcode'));
		add_action('init', array($this, 'create_custom_rewrite_rules'));
		add_filter('query_vars', array($this, 'add_custom_page_variables'));

		add_filter( 'author_link', array( $this, 'modify_author_link' ), 10, 2 );

		add_shortcode('wre_archive_agent', array($this, 'wre_archive_agent'));
	}

	/**
	 * Check if we have the shortcode displayed
	 */
	public function has_shortcode() {
		global $post;
		if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'wre_archive_agent')) {
			add_filter('is_wre', array($this, 'is_wre'));
		}
	}

	/**
	 * Add this as a wre page
	 *
	 * @param bool $return
	 * @return bool
	 */
	public function is_wre($return) {
		return true;
	}

	/**
	 * add_custom_page_variables()
	 * Add the custom token as an allowed query variable.
	 * return array $public_query_vars.
	 * */
	function add_custom_page_variables($public_query_vars) {
		$public_query_vars[] = 'agent';
		return $public_query_vars;
	}

	/**
	 * create_custom_rewrite_rules()
	 * Creates the custom rewrite rules.
	 * */
	function create_custom_rewrite_rules() {
		$rules = get_option('rewrite_rules');
		$agent_page = wre_option( 'wre_single_agent' );
		$slug = get_post_field( 'post_name', $agent_page );
		// Remember to flush the rules once manually after you added this code!
		add_rewrite_rule(
			// The regex to match the incoming URL
			$slug.'/([^/]+)/?',
			// The resulting internal URL: `index.php` because we still use WordPress
			// `pagename` because we use this WordPress page
			// `designer_slug` because we assign the first captured regex part to this variable
			'index.php?pagename='.$slug.'&agent=$matches[1]',
			// This is a rather specific URL, so we add it to the top of the list
			// Otherwise, the "catch-all" rules at the bottom (for pages and attachments) will "win"
			'top' );
		if(! isset($rules[$slug.'/([^/]+)/?'])) {
			flush_rewrite_rules(true);
		}
	}

	public static function wre_archive_agent($atts) {
		global $wp_query;
		$agent = get_query_var('agent');
		if ($agent) {

			if (username_exists($agent)) {

				$agent_data = get_user_by( 'login', $agent );
				$roles = $agent_data->roles;

				if (in_array('wre_agent', $roles) || in_array('administrator', $roles)) {

					$agent_id = $agent_data->ID;

					ob_start();

					/**
					 * @hooked wre_output_content_wrapper (outputs opening divs for the content)
					 */
					do_action('wre_before_main_content');

					do_action('wre_before_single_agent');

					$show_agents_listings = wre_option('show_agents_listings') ? wre_option('show_agents_listings') : 'yes';
					?>

					<div class="wre-single agent">

						<div class="main-wrap full-width" itemscope itemtype="http://schema.org/ProfilePage">

							<div class="summary">
								<div class="wre-social-icons-wrapper">
									<?php do_action('wre_single_agent_intro', $agent_id); ?>
								</div>
								<div class="agent-details-wrapper wre-agent-details">
									<?php do_action('wre_single_agent_summary', $agent_id); ?>
								</div>
							</div>

							<div class="content">
								<?php do_action('wre_single_agent_content', $agent_id); ?>
							</div>

							<?php if ($show_agents_listings == 'yes') { ?>
								<div class="bottom">
									<?php do_action('wre_single_agent_bottom', $agent_id); ?>
								</div>
							<?php } ?>
						</div>
					</div>

					<?php
					do_action('wre_after_single_agent');

					/**
					 * @hooked wre_output_content_wrapper_end (outputs closing divs for the content)
					 */
					do_action('wre_after_main_content');
					return ob_get_clean();
				}
			}
		}

		return '<p>' . __('No profile found or it\'s not available now!', 'wp-real-estate') . '</p>';
	}
	
	public function modify_author_link( $link, $author_id ) {

		$agent = get_user_by( 'ID', $author_id );
		if ( in_array( 'wre_agent', $agent->roles ) || in_array( 'administrator', $agent->roles ) ) {
			$agent_page = wre_option( 'wre_single_agent' );
			$link = get_permalink( $agent_page ).$agent->user_login;
		}

		return $link;	 	  	 	 
	}

}

// Instantiate class.
return new WRE_Agent_Rewrite();