<?php
// Register and load the widget
if (!function_exists('wre_load_widget')) {

	function wre_load_widget() {
		register_widget('WRE_Widget');
		register_widget('WRE_Recent_Listings');
		register_widget('WRE_Search_Listings');
		register_widget('WRE_NearBy_Listings');
		register_widget('WRE_Agents');
	}

}
add_action('widgets_init', 'wre_load_widget');

if (!class_exists('WRE_Widget')) {

	class WRE_Widget extends WP_Widget {

		function __construct() {
			parent::__construct(
					'wre_widget', __('WRE Widget', 'wp-real-estate'), array('description' => __('Use this widget to display Agent Details and Agent Listings on there respective pages.', 'wp-real-estate'),)
			);
		}

		// Creating widget front-end
		public function widget($args, $instance) {
			$is_agent = false;
			if (is_author()) {
				$user = new WP_User(wre_agent_ID());
				$user_roles = $user->roles;
				$listings_count = wre_agent_listings_count(wre_agent_ID());
				if (in_array('wre_agent', $user_roles) || $listings_count > 0) {
					$is_agent = true;
				}
			}
			if (is_singular('listing') || $is_agent ) {
				// before and after widget arguments are defined by themes
				echo $args['before_widget'];
				?>
				<div class="wre-sidebar">
					<?php
					if (is_singular('listing'))
						do_action('wre_single_listing_sidebar');
					if ($is_agent)
						do_action('wre_single_agent_sidebar');
					?>
				</div>
				<?php
				echo $args['after_widget'];
			}
		}

	}

	// Class wre_widget ends here
}

if (!class_exists('WRE_Recent_Listings')) {

	class WRE_Recent_Listings extends WP_Widget {

		public function __construct() {
			$widget_ops = array(
				'classname' => 'wre_recent_listings',
				'description' => __("Your site's most recent Listings.", "wp-real-estate"),
				'customize_selective_refresh' => true,
			);
			parent::__construct('wre-recent-listings', __('WRE Recent Listings', 'wp-real-estate'), $widget_ops);
			$this->alt_option_name = 'wre_recent_listings';
		}

		public function widget($args, $instance) {
			if (!isset($args['widget_id'])) {
				$args['widget_id'] = $this->id;
			}

			$title = (!empty($instance['title']) ) ? $instance['title'] : __('Recent Listings', 'wp-real-estate');

			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			$number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;
			if (!$number)
				$number = 5;

			echo $args['before_widget'];
			if ($title) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			echo do_shortcode('[wre_listings number="' . $number . '" compact="true"]');
			echo $args['after_widget'];
		}

		public function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = sanitize_text_field($new_instance['title']);
			$instance['number'] = (int) $new_instance['number'];
			return $instance;
		}

		public function form($instance) {
			$title = isset($instance['title']) ? esc_attr($instance['title']) : '';
			$number = isset($instance['number']) ? absint($instance['number']) : 5;
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of listings to show:', 'wp-real-estate'); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo $number; ?>" size="3" />
			</p>

			<?php
		}

	}

}

if (!class_exists('WRE_Search_Listings')) {

	class WRE_Search_Listings extends WP_Widget {

		public function __construct() {
			$widget_ops = array(
				'classname' => 'wre_search_listings',
				'description' => __('Use this widget to display search listings form', 'wp-real-estate'),
				'customize_selective_refresh' => true,
			);
			parent::__construct('wre-search-listings', __('WRE Search Listings', 'wp-real-estate'), $widget_ops);
			$this->alt_option_name = 'wre_search_listings';
		}

		public function widget($args, $instance) {
			if (!isset($args['widget_id'])) {
				$args['widget_id'] = $this->id;
			}

			$title = (!empty($instance['title']) ) ? $instance['title'] : __('Search Listings', 'wp-real-estate');
			$placeholder_text = (!empty($instance['placeholder-text']) ) ? $instance['placeholder-text'] : __('Search by Keyword, City or State', 'wp-real-estate');
			$submit_button_text = (!empty($instance['submit-button-text']) ) ? $instance['submit-button-text'] : __('Search', 'wp-real-estate');
			$exclude_fields = (!empty($instance['exclude-fields']) ) ? $instance['exclude-fields'] : '';
			if ($exclude_fields)
				$exclude_fields = implode(', ', $exclude_fields);

			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			echo $args['before_widget'];
			if ($title) {
				echo $args['before_title'] . $title . $args['after_title'];
			}
			echo do_shortcode('[wre_search placeholder="' . $placeholder_text . '" submit_btn="' . $submit_button_text . '" exclude="' . $exclude_fields . '"]');
			echo $args['after_widget'];
		}

		public function update($new_instance, $old_instance) {
			$instance = $old_instance;

			$instance['title'] = sanitize_text_field($new_instance['title']);
			$instance['placeholder-text'] = sanitize_text_field($new_instance['placeholder-text']);
			$instance['submit-button-text'] = sanitize_text_field($new_instance['submit-button-text']);
			$instance['exclude-fields'] = $new_instance['exclude-fields'];
			return $instance;
		}

		public function form($instance) {
			$instance = wp_parse_args((array) $instance, array('title' => '', 'placeholder-text' => '', 'submit-button-text' => '', 'exclude-fields' => ''));
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('placeholder-text'); ?>"><?php _e('Placeholder Text:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('placeholder-text'); ?>" name="<?php echo $this->get_field_name('placeholder-text'); ?>" type="text" value="<?php echo esc_attr($instance['placeholder-text']); ?>" />
				<small><?php _e('Text to display as the placeholder text in the text input.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('submit-button-text'); ?>"><?php _e('Submit Button Text:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('submit-button-text'); ?>" name="<?php echo $this->get_field_name('submit-button-text'); ?>" type="text" value="<?php echo esc_attr($instance['submit-button-text']); ?>" />
				<small><?php _e('Text to display on the search button.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('exclude-fields'); ?>"><?php _e('Exclude Fields:', 'wp-real-estate'); ?></label>
					<?php $search_fields = array('type', 'min_beds', 'max_beds', 'min_price', 'max_price'); ?>
				<select class="widefat" id="<?php echo $this->get_field_id('exclude-fields'); ?>" name="<?php echo $this->get_field_name('exclude-fields'); ?>[]" multiple="true">
					<option value=""><?php _e('Exclude Fields', 'wp-real-estate'); ?></option>
					<?php
					foreach ($search_fields as $search_field) {
						$selected = is_array($instance['exclude-fields']) && in_array($search_field, $instance['exclude-fields']) ? ' selected="selected" ' : '';
						echo '<option value="' . $search_field . '" ' . $selected . '>' . ucwords(str_replace('_', ' ', $search_field)) . '</option>';
					}
					?>
				</select>
				<small><?php _e('Select list of fields that you don\'t want to include on the search box.', 'wp-real-estate') ?></small>
			</p>
			<?php
		}

	}

}

if (!class_exists('WRE_NearBy_Listings')) {

	class WRE_NearBy_Listings extends WP_Widget {

		public function __construct() {
			$widget_ops = array(
				'classname' => 'wre_nearby_listings',
				'description' => __('Nearby Listings to be shown.', 'wp-real-estate'),
				'customize_selective_refresh' => true,
			);
			parent::__construct('wre-nearby-listings', __('WRE NearBy Listings', 'wp-real-estate'), $widget_ops);
			$this->alt_option_name = 'wre_nearby_listings';
		}

		public function widget($args, $instance) {
			if (!isset($args['widget_id'])) {
				$args['widget_id'] = $this->id;
			}

			$key = wre_map_key();
			if (!$key)
				return;

			$title = (!empty($instance['title']) ) ? $instance['title'] : __('Nearby Listings', 'wp-real-estate');
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			$number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;

			$distance = isset($instance['distance']) ? esc_attr($instance['distance']) : 'miles';
			$radius = isset($instance['radius']) ? absint($instance['radius']) : 50;

			$listing_view = isset($instance['listing-view']) ? esc_attr($instance['listing-view']) : '';
			$columns = isset($instance['listing-columns']) ? absint($instance['listing-columns']) : '';
			$compact = isset($instance['compact-mode']) ? $instance['compact-mode'] : '';

			echo $args['before_widget'];
			if ($title) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			echo do_shortcode('[wre_nearby_listings compact="' . $compact . '" distance="' . $distance . '" radius="' . $radius . '" number="' . $number . '" view="' . $listing_view . '" columns="' . $columns . '" ]');

			echo $args['after_widget'];
		}

		public function update($new_instance, $old_instance) {
			$instance = $old_instance;

			$instance['title'] = sanitize_text_field($new_instance['title']);
			$instance['number'] = (int) $new_instance['number'];
			if (in_array($new_instance['distance'], array('miles', 'kilometers'))) {
				$instance['distance'] = $new_instance['distance'];
			} else {
				$instance['distance'] = 'miles';
			}
			if (in_array($new_instance['listing-view'], array('list-view', 'grid-view'))) {
				$instance['listing-view'] = $new_instance['listing-view'];
			} else {
				$instance['listing-view'] = 'list-view';
			}
			if (in_array($new_instance['listing-columns'], array('2', '3', '4'))) {
				$instance['listing-columns'] = (int) $new_instance['listing-columns'];
			} else {
				$instance['listing-columns'] = '3';
			}
			if (in_array($new_instance['compact-mode'], array('true', 'false'))) {
				$instance['compact-mode'] = $new_instance['compact-mode'];
			} else {
				$instance['compact-mode'] = 'true';
			}
			$instance['radius'] = (int) $new_instance['radius'];

			return $instance;
		}

		public function form($instance) {

			$instance = wp_parse_args((array) $instance, array(
				'title' => '',
				'number' => 5,
				'distance' => 'miles',
				'radius' => 50,
				'listing-view' => 'list-view',
				'listing-columns' => 3,
				'compact-mode' => true
					));
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('distance'); ?>"><?php _e('Distance Measurement:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('distance'); ?>" name="<?php echo $this->get_field_name('distance'); ?>">
					<option value="miles" <?php selected($instance['distance'], 'miles'); ?>><?php _e('Miles', 'wp-real-estate'); ?></option>
					<option value="kilometers" <?php selected($instance['distance'], 'kilometers'); ?>><?php _e('Kilometers', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('Choose miles or kilometers for the radius.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('radius'); ?>"><?php _e('Radius:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('radius'); ?>" name="<?php echo $this->get_field_name('radius'); ?>" type="number" value="<?php echo esc_attr($instance['radius']); ?>" />
				<small><?php _e('Show listings that are within this distance (mi or km as selected above).', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('listing-view'); ?>"><?php _e('List View:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('listing-view'); ?>" name="<?php echo $this->get_field_name('listing-view'); ?>">
					<option value="list-view" <?php selected($instance['listing-view'], 'list-view'); ?>><?php _e('List View', 'wp-real-estate'); ?></option>
					<option value="grid-view" <?php selected($instance['listing-view'], 'grid-view'); ?>><?php _e('Grid View', 'wp-real-estate'); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('listing-columns'); ?>"><?php _e('List Columns:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('listing-columns'); ?>" name="<?php echo $this->get_field_name('listing-columns'); ?>">
					<option value="2" <?php selected($instance['listing-columns'], '2'); ?>><?php _e('2 columns', 'wp-real-estate'); ?></option>
					<option value="3" <?php selected($instance['listing-columns'], '3'); ?>><?php _e('3 columns', 'wp-real-estate'); ?></option>
					<option value="4" <?php selected($instance['listing-columns'], '4'); ?>><?php _e('4 columns', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('The number of columns to display, when viewing listings in grid mode.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('compact-mode'); ?>"><?php _e('Compact Mode:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('compact-mode'); ?>" name="<?php echo $this->get_field_name('compact-mode'); ?>">
					<option value="true" <?php selected($instance['compact-mode'], 'true'); ?>><?php _e('True', 'wp-real-estate'); ?></option>
					<option value="false" <?php selected($instance['compact-mode'], 'false'); ?>><?php _e('False', 'wp-real-estate'); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of listings to show:', 'wp-real-estate'); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo absint($instance['number']); ?>" size="3" />
			</p>

			<?php
		}

	}

}

if (!class_exists('WRE_Agents')) {

	class WRE_Agents extends WP_Widget {

		public function __construct() {
			$widget_ops = array(
				'classname' => 'wre_agents',
				'description' => __('Display agents either in a carousel or listing view.', 'wp-real-estate'),
				'customize_selective_refresh' => true,
			);
			parent::__construct('wre-agents', __('WRE Agents', 'wp-real-estate'), $widget_ops);
			$this->alt_option_name = 'wre_agents';
		}

		public function widget($args, $instance) {
			if (!isset($args['widget_id'])) {
				$args['widget_id'] = $this->id;
			}

			$title = (!empty($instance['title']) ) ? $instance['title'] : __('Agents you can trust', 'wp-real-estate');
			$title = apply_filters('widget_title', $title, $instance, $this->id_base);

			$number = (!empty($instance['number']) ) ? absint($instance['number']) : 5;

			$view = isset($instance['view']) ? esc_attr($instance['view']) : '';
			$autoplay = isset($instance['autoplay']) ? esc_attr($instance['autoplay']) : '';
			$dots = isset($instance['dots']) ? esc_attr($instance['dots']) : '';
			$controls = isset($instance['controls']) ? esc_attr($instance['controls']) : '';
			$loop = isset($instance['loop']) ? esc_attr($instance['loop']) : '';
			$items = isset($instance['items']) ? absint($instance['items']) : '';
			$agents_view = isset($instance['agents-view']) ? esc_attr($instance['agents-view']) : '';
			$agent_columns = isset($instance['agent-columns']) ? absint($instance['agent-columns']) : '';

			echo $args['before_widget'];
			if ($title) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			echo do_shortcode('[wre_agents view="' . $view . '" autoplay="' . $autoplay . '" dots="' . $dots . '" controls="' . $controls . '" loop="' . $loop . '" items="' . $items . '" number="' . $number . '" agents-view="' . $agents_view . '" agent-columns="' . $agent_columns . '" allow_pagination="no"]');

			echo $args['after_widget'];
		}

		public function update($new_instance, $old_instance) {
			$instance = $old_instance;
			$instance['title'] = sanitize_text_field($new_instance['title']);
			$instance['number'] = (int) $new_instance['number'];
			if (in_array($new_instance['view'], array('lists', 'carousel'))) {
				$instance['view'] = $new_instance['view'];
			} else {
				$instance['view'] = 'lists';
			}
			if (in_array($new_instance['autoplay'], array('true', 'false'))) {
				$instance['autoplay'] = $new_instance['autoplay'];
			} else {
				$instance['autoplay'] = 'true';
			}
			if (in_array($new_instance['dots'], array('true', 'false'))) {
				$instance['dots'] = $new_instance['dots'];
			} else {
				$instance['dots'] = 'true';
			}
			if (in_array($new_instance['controls'], array('true', 'false'))) {
				$instance['controls'] = $new_instance['controls'];
			} else {
				$instance['controls'] = 'true';
			}
			if (in_array($new_instance['loop'], array('true', 'false'))) {
				$instance['loop'] = $new_instance['loop'];
			} else {
				$instance['loop'] = 'true';
			}
			$instance['items'] = (int) $new_instance['items'];

			if (in_array($new_instance['agents-view'], array('list-view', 'grid-view'))) {
				$instance['agents-view'] = $new_instance['agents-view'];
			} else {
				$instance['agents-view'] = 'list-view';
			}
			if (in_array($new_instance['agent-columns'], array(1, 2, 3, 4))) {
				$instance['agent-columns'] = (int) $new_instance['agent-columns'];
			} else {
				$instance['agent-columns'] = '3';
			}

			return $instance;
		}

		public function form($instance) {

			$instance = wp_parse_args((array) $instance, array(
				'title' => '',
				'view' => 'lists',
				'autoplay' => true,
				'dots' => true,
				'controls' => true,
				'loop' => true,
				'items' => 2,
				'agents-view' => 'list-view',
				'agent-columns' => 2,
				'number' => 5
					));
			?>
			<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($instance['title']); ?>" />
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('view'); ?>"><?php _e('View:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('view'); ?>" name="<?php echo $this->get_field_name('view'); ?>">
					<option value="lists" <?php selected($instance['view'], 'lists'); ?>><?php _e('Lists', 'wp-real-estate'); ?></option>
					<option value="carousel" <?php selected($instance['view'], 'carousel'); ?>><?php _e('Carousel', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('Select option to display agents either in a carousel or listing view', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('agents-view'); ?>"><?php _e('Agents View:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('agents-view'); ?>" name="<?php echo $this->get_field_name('agents-view'); ?>">
					<option value="list-view" <?php selected($instance['agents-view'], 'list-view'); ?>><?php _e('List View', 'wp-real-estate'); ?></option>
					<option value="grid-view" <?php selected($instance['agents-view'], 'grid-view'); ?>><?php _e('Grid View', 'wp-real-estate'); ?></option>
				</select>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('agent-columns'); ?>"><?php _e('Agent Columns:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('agent-columns'); ?>" name="<?php echo $this->get_field_name('agent-columns'); ?>">
					<option value="1" <?php selected($instance['agent-columns'], '1'); ?>><?php _e('1 column', 'wp-real-estate'); ?></option>
					<option value="2" <?php selected($instance['agent-columns'], '2'); ?>><?php _e('2 columns', 'wp-real-estate'); ?></option>
					<option value="3" <?php selected($instance['agent-columns'], '3'); ?>><?php _e('3 columns', 'wp-real-estate'); ?></option>
					<option value="4" <?php selected($instance['agent-columns'], '4'); ?>><?php _e('4 columns', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('The number of columns to display, when viewing agents in grid mode.', 'wp-real-estate') ?></small>
			</p>
			<br />
			<strong><?php _e('Carousel Settings', 'wp-real-estate'); ?></strong>
			<p>
				<label for="<?php echo $this->get_field_id('autoplay'); ?>"><?php _e('Autoplay:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('autoplay'); ?>" name="<?php echo $this->get_field_name('autoplay'); ?>">
					<option value="true" <?php selected($instance['autoplay'], 'true'); ?>><?php _e('True', 'wp-real-estate'); ?></option>
					<option value="false" <?php selected($instance['autoplay'], 'false'); ?>><?php _e('False', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('If true, the Slider will automatically start to play.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('dots'); ?>"><?php _e('Dots:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('dots'); ?>" name="<?php echo $this->get_field_name('dots'); ?>">
					<option value="true" <?php selected($instance['dots'], 'true'); ?>><?php _e('True', 'wp-real-estate'); ?></option>
					<option value="false" <?php selected($instance['dots'], 'false'); ?>><?php _e('False', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('Show dots below the slider.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('controls'); ?>"><?php _e('Controls:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('controls'); ?>" name="<?php echo $this->get_field_name('controls'); ?>">
					<option value="true" <?php selected($instance['controls'], 'true'); ?>><?php _e('True', 'wp-real-estate'); ?></option>
					<option value="false" <?php selected($instance['controls'], 'false'); ?>><?php _e('False', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('If false, prev/next buttons will not be displayed.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('loop'); ?>"><?php _e('Loop:', 'wp-real-estate'); ?></label>
				<select class="widefat" id="<?php echo $this->get_field_id('loop'); ?>" name="<?php echo $this->get_field_name('loop'); ?>">
					<option value="true" <?php selected($instance['loop'], 'true'); ?>><?php _e('True', 'wp-real-estate'); ?></option>
					<option value="false" <?php selected($instance['loop'], 'false'); ?>><?php _e('False', 'wp-real-estate'); ?></option>
				</select>
				<small><?php _e('If false, will disable the ability to loop back to the beginning of the slide when on the last element.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('items'); ?>"><?php _e('Items:', 'wp-real-estate'); ?></label>
				<input class="widefat" id="<?php echo $this->get_field_id('items'); ?>" name="<?php echo $this->get_field_name('items'); ?>" type="number" step="1" min="1" value="<?php echo absint($instance['items']); ?>" size="3" />
				<small><?php _e('How many agents to show at once if carousel option is selected above.', 'wp-real-estate') ?></small>
			</p>

			<p>
				<label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number of agents to show:', 'wp-real-estate'); ?></label>
				<input class="tiny-text" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="number" step="1" min="1" value="<?php echo absint($instance['number']); ?>" size="3" />
			</p>

			<?php
		}

	}

}