<?php
/**
 * Pagination
 *
 * This template can be overridden by copying it to yourtheme/listings/loop/pagination.php.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$contextual_query = wre_get_contextual_query();
if ( $contextual_query->max_num_pages <= 1 ) {
	return;
}
$paged = ( get_query_var('paged') );
if ( ! $paged && isset( $_GET['paged'] ) && $_GET['paged'] != '' ) {
	$paged = $_GET['paged'];
} else if( $paged == 0 ) {
	$paged = 1;
}

$orderby = '';
if( isset( $_GET['wre-orderby'] ) && $_GET['wre-orderby'] != '' ) {
	$orderby = $_GET['wre-orderby'];
}

?>
<nav class="wre-pagination" data-orderby="<?php echo esc_attr( $orderby ); ?>">
	<?php
	echo paginate_links( apply_filters( 'wre_pagination_args', array(
		'base'		=> add_query_arg('paged','%#%'),
		'format'	=> '?paged=%#%',
		'mid-size'	=> 1,
		'add_args'	=> false,
		'current'	=> $paged,
		'total'		=> $contextual_query->max_num_pages,
		'prev_text'	=> '&larr;',
		'next_text'	=> '&rarr;',
		'type'		=> 'list',
		'end_size'	=> 3,
	) ) );
	?>
</nav>