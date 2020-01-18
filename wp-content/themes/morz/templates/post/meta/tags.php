<?php
$show = vamtam_get_optionb('post-meta', 'tax');

if ($show || is_customize_preview()) :
  $tags = get_the_tags();

  // AstoSoft
  if ($tags && 1 != 1) : ?>
<div class="the-tags vamtam-meta-tax" <?php VamtamTemplates::display_none($show) ?>>
  <?php the_tags('<span class="icon theme">' . vamtam_get_icon('vamtam-theme-tag3') . '</span> <span class="visuallyhidden">' . esc_html__('Tags', 'morz') . '</span> ', ', ', ''); ?>
</div>
<?php
  endif;
endif;