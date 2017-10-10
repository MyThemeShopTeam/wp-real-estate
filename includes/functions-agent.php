<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly.

/*
 * Get an agents ID
 */
function wre_agent_ID() {

	$id = wre_meta( 'agent' );
	if( $id ) {
		return $id;
	} else {
		// gets the author when on single agent page
		if( wre_is_theme_compatible() ) {
			$curauth = (get_query_var('agent')) ? get_user_by('slug', get_query_var('agent')) : get_userdata(get_query_var('agent'));
		} else {
			$curauth = (get_query_var('author_name')) ? get_user_by('slug', get_query_var('author_name')) : get_userdata(get_query_var('author'));
		}
		if( $curauth ) {
			return $curauth->ID;
		}
	}

	// if nothing above, then check for shortcode
	return wre_shortcode_att( 'id', 'wre_agent' );

}

/*
 * Get count of individual agents listings
 */
function wre_agent_listings_count( $agent_id ) {

	$args = array(
		'post_type'			=> 'listing',
		'posts_per_page'	=> '-1',
		'post_status'		=> 'publish',
		'meta_query'		=> array(
			array(
				'key'		=> '_wre_listing_agent',
				'value'		=> $agent_id,
				'compare'	=> '=',
			),
		),
	);

	$listings = query_posts( $args );

	$count = $listings ? count( $listings ) : '0';

	/* Restore original Post Data */
	wp_reset_query();

	return $count > 0 ? $count : '0';
}

/**
 * Helper function get getting roles that the user is allowed to create/edit/delete.
 *
 * @param   WP_User $user
 * @return  array
 */
function wre_get_allowed_roles( $user ) {
	$allowed = array();

	if ( in_array( 'wre_agent', $user->roles ) ) {
		$allowed[] = 'wre_agent';
	}

	return $allowed;
}

/**
 * Remove roles that are not allowed for the current user role.
 */
function wre_editable_roles( $roles ) {
	$user = wp_get_current_user();
	if ( in_array( 'wre_agent', $user->roles ) ) {
		$allowed = wre_get_allowed_roles( $user );

		foreach ( $roles as $role => $caps ) {
			if ( ! in_array( $role, $allowed ) )
				unset( $roles[ $role ] );
		}
	}

	return $roles;
}
add_filter( 'editable_roles', 'wre_editable_roles' );

/**
 * Prevent users deleting/editing users with a role outside their allowance.
 */
function wre_map_meta_cap( $caps, $cap, $user_ID, $args ) {

	if ( ( $cap === 'edit_user' || $cap === 'delete_user' ) && $args ) {
		$the_user = get_userdata( $user_ID ); // The user performing the task
		$user     = get_userdata( $args[0] ); // The user being edited/deleted

		// only run for agents
		if ( in_array( 'wre_agent', $the_user->roles ) ) {
			if ( $the_user && $user && $the_user->ID != $user->ID /* User can always edit self */ ) {
				$allowed = wre_get_allowed_roles( $the_user );
				if ( array_diff( $user->roles, $allowed ) ) {
					// Target user has roles outside of our limits
					$caps[] = 'not_allowed';
				}
			}
		}
	}

	return $caps;
}
add_filter( 'map_meta_cap', 'wre_map_meta_cap', 10, 4 );

/**
 * Retrieve the appropriate image size
 *
 * @param int    $user_id      Default: $post->post_author. Will accept any valid user ID passed into this parameter.
 * @param string $size         Default: 'thumbnail'. Accepts all default WordPress sizes and any custom sizes made by
 *                             the add_image_size() function.
 *
 * @return string      (Url) Use this inside the src attribute of an image tag or where you need to call the image url.
 */
function get_wre_img_meta( $user_id, $size = 'thumbnail' ) {
	global $post;

	if ( ! $user_id || ! is_numeric( $user_id ) ) {
		/*
		 * Here we're assuming that the avatar being called is the author of the post.
		 * The theory is that when a number is not supplied, this function is being used to
		 * get the avatar of a post author using get_avatar() and an email address is supplied
		 * for the $id_or_email parameter. We need an integer to get the custom image so we force that here.
		 * Also, many themes use get_avatar on the single post pages and pass it the author email address so this
		 * acts as a fall back.
		 */
		$user_id = $post->post_author;
	}

	// Check first for a custom uploaded image.
	$attachment_upload_url = esc_url( get_the_author_meta( 'wre_upload_meta', $user_id ) );

	if ( $attachment_upload_url ) {
		// Grabs the id from the URL using the WordPress function attachment_url_to_postid @since 4.0.0.
		$attachment_id = attachment_url_to_postid( $attachment_upload_url );

		// Retrieve the thumbnail size of our image. Should return an array with first index value containing the URL.
		$image_thumb = wp_get_attachment_image_src( $attachment_id, $size );

		return isset( $image_thumb[0] ) ? $image_thumb[0] : '';
	}

	// Finally, check for image from an external URL. If none exists, return an empty string.
	$attachment_ext_url = esc_url( get_the_author_meta( 'wre_meta', $user_id ) );

	return $attachment_ext_url ? $attachment_ext_url : '';
}

/**
 * WordPress Avatar Filter
 *
 * Replaces the WordPress avatar with your custom photo using the get_avatar hook.
 *
 * @param string			$avatar     Image tag for the user's avatar.
 * @param int|object|string	$identifier User object, UD or email address.
 * @param string            $size       Image size.
 * @param string            $alt        Alt text for the image tag.
 *
 * @return string
 */
function wre_filter_avatar( $avatar, $identifier, $size, $alt ) {
	if ( $user = wre_get_user_by_id_or_email( $identifier ) ) {
		if ( $custom_avatar = get_wre_img_meta( $user->ID, 'thumbnail' ) ) {
			return "<img alt='{$alt}' src='{$custom_avatar}' class='avatar avatar-{$size} photo' height='{$size}' width='{$size}' />";
		}
	}

	return $avatar;
}
add_filter( 'get_avatar', 'wre_filter_avatar', 1, 5 );

/**
 * Get a WordPress User by ID or email
 *
 * @param int|object|string $identifier User object, ID or email address.
 *
 * @return WP_User
 */
function wre_get_user_by_id_or_email( $identifier ) {
	// If an integer is passed.
	if ( is_numeric( $identifier ) ) {
		return get_user_by( 'id', (int) $identifier );
	}

	// If the WP_User object is passed.
	if ( is_object( $identifier ) && property_exists( $identifier, 'ID' ) ) {
		return get_user_by( 'id', (int) $identifier->ID );
	}

	// If the WP_Comment object is passed.
	if ( is_object( $identifier ) && property_exists( $identifier, 'user_id' ) ) {
		return get_user_by( 'id', (int) $identifier->user_id );
	}

	return get_user_by( 'email', $identifier );
}