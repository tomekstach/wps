<?php

/**
 * Catch-all post loop
 */

// display full post/image or thumbs
if (!isset($called_from_shortcode)) {
  $settings = new stdClass;

  $settings->show_title   = true;
  $settings->show_media   = true;
  $settings->pagination   = true;
  $settings->layout       = vamtam_extra_features() ? vamtam_get_option('archive-layout') : 'normal';
  $settings->show_content = $settings->layout !== 'normal';
  $settings->columns      = $settings->layout === 'normal' ? 1 : 2;
  $settings->gap          = true;

  $news        = 'mosaic' === $settings->layout;
  $max_columns = 0;
}

if (defined('VAMTAM_ARCHIVE_TEMPLATE') && !$news) {
  $settings->show_content = false;
}

global $vamtam_loop_vars;
$old_vamtam_loop_vars = $vamtam_loop_vars;

$vamtam_loop_vars = array(
  'show_content' => $settings->show_content,
  'show_title'   => $settings->show_title,
  'show_media'   => $settings->show_media,
  'news'         => $news,
  'columns'      => $settings->columns,
  'layout'       => $settings->layout,
);

$is_cube = in_array($settings->layout, array('mosaic', 'small'), true);

$wrapper_class = array();

$wrapper_class[] = $news ? 'news' : 'regular';
$wrapper_class[] = $settings->layout;

// AstoSoft - start
if ($news && !$is_cube) {
  $wrapper_class[] = 'row';
}
// AstoSoft - end

$cube_options         = array();
$data_options_escaped = '';

if ($is_cube) {
  $cube_options = array(
    'layoutMode'        => $settings->layout === 'small' ? 'grid' : $settings->layout,
    'sortToPreventGaps' => true,
    'defaultFilter'     => '*',
    'animationType'     => 'quicksand',
    'gapHorizontal'     => $settings->gap ? 30 : 0,
    'gapVertical'       => $settings->gap ? 30 : 0,
    'gridAdjustment'    => 'responsive',
    'mediaQueries'      => VamtamTemplates::scrollable_columns($max_columns),
    'displayType'       => 'bottomToTop',
    'displayTypeSpeed'  => 100,
  );

  $wrapper_class[] = 'vamtam-cubeportfolio cbp';

  $data_options_escaped = 'data-options="' . esc_attr(json_encode($cube_options)) . '"';

  if (VamtamTemplates::early_cube_load()) {
    wp_enqueue_style('cubeportfolio');
    wp_enqueue_script('cubeportfolio');
  }

  $GLOBALS['vamtam_inside_cube'] = true;

  // print late styles, otherwise Beaver will skip over some of them
  if (!doing_filter('get_the_excerpt')) {
    print_late_styles();
  }
}

?>
<div class="loop-wrapper clearfix <?php echo esc_attr(implode(' ', $wrapper_class)) ?>"
  data-columns="<?php echo esc_attr($settings->columns) ?>"
  <?php echo $data_options_escaped // xss ok 
                                                                                                                                                  ?>>
  <?php

  do_action('vamtam_before_main_loop');

  $i = 0;

  if (!isset($blog_query)) {
    $blog_query = $GLOBALS['wp_query'];
  } else {
    // ideally, this shouldn't be necessary, but for some reason Beaver Builder
    // uses the post ID from the global $wp_query instead of the global $post
    $GLOBALS['wp_query'] = $blog_query;
  }

  if ($blog_query->have_posts()) :
    while ($blog_query->have_posts()) : $blog_query->the_post();
      $post_class   = array();
      $post_class[] = 'page-content post-header';

      if ($settings->columns === 1 && !$news) {
        $post_class[] = 'clearfix';
      }

      if ($news && 0 === $i % $settings->columns) {
        $post_class[] = 'clearboth';
      }

      if (!$blog_query->is_single()) {
        $post_class[] = 'list-item';
      }

      if ($is_cube) {
        $post_class[] = 'cbp-item';
      }

      $starting_width = 100 / $settings->columns;
  ?>
  <div <?php post_class(implode(' ', $post_class)) ?> style="width: <?php echo esc_attr($starting_width) ?>%">
    <div>
      <?php include locate_template('templates/post.php');  ?>
    </div>
  </div>
  <?php
      $i++;
    endwhile;
  endif;

  wp_reset_query();

  do_action('vamtam_after_main_loop');
  ?>
</div>

<?php

if (vamtam_sanitize_bool($settings->pagination)) {
  $pagination_type = vamtam_get_option('pagination-type');

  if ('mosaic' !== $settings->layout || defined('VAMTAM_ARCHIVE_TEMPLATE')) {
    $pagination_type = 'paged';
  }

  VamtamTemplates::pagination($pagination_type, true, $vamtam_loop_vars, $blog_query);
}

if ($settings->layout === 'mosaic' || $settings->layout === 'grid') {
  $GLOBALS['vamtam_inside_cube'] = false;
}

$vamtam_loop_vars = $old_vamtam_loop_vars;