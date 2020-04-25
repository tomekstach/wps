<?php

/**
 * 404 page template
 *
 * @package vamtam/morz
 */
// AstoSoft - start
$user = wp_get_current_user();

$slug_array = mytheme_get_slugs($_SERVER['REQUEST_URI']);
$the_slug   = end($slug_array);

if (the_slug_exists($the_slug)) {
  $page_title   = $the_slug;
  $post_status  = 'private';
} else {
  $page_title   = false;
  $post_status  = false;
}

/*if (is_object($page)) {
  $page_title = get_the_title($page);
  $post_status = $page->post_status;
} else {
  $page_title   = false;
  $post_status  = false;
}*/

if ($page_title !== false && $post_status == 'private' && (in_array('registered', $user->roles) || in_array('registeredbiuro', $user->roles) || in_array('subscriber', $user->roles))) {
  wp_redirect(get_site_url() . '/dziekujemy-za-rejestracje/');
} else if ($page_title !== false && $post_status == 'private' && $user->ID > 0) {
  wp_redirect(get_site_url() . '/rejestracja/');
} else if ($page_title !== false && $post_status == 'private' && $user->ID == 0) {
  wp_redirect(get_site_url() . '/logowanie/?redirect_to=' . str_replace(home_url( '/' ), '', $_SERVER['REQUEST_URI']));
}
// AstoSoft - end
get_header();

VamtamEnqueues::enqueue_style_and_print('vamtam-not-found');

?>

<div class="clearfix">
	<div id="header-404">
		<div class="line-1"><?php echo esc_html_x( '404', 'page not found error', 'morz' ) ?></div>
		<div class="line-2"><?php esc_html_e( 'Holy guacamole!', 'morz' ) ?></div>
		<div class="line-3"><?php esc_html_e( 'Looks like this page is on vacation. Or just playing hard to get. At any rate... it is not here.', 'morz' ) ?></div>
		<div class="line-4"><a href="<?php echo esc_url( home_url( '/' ) ) ?>"><?php echo esc_html__( '&larr; Go to the home page or just search...', 'morz' ) ?></a></div>
	</div>
	<div class="page-404">
		<?php get_search_form(); ?>
	</div>
</div>

<?php get_footer(); ?>

