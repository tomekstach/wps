<?php
// AstoSoft
/*if (!get_option('vamtam_last_import_map')) {
  return;
}*/

$header_text_main = vamtam_get_option('header-text-main');

$has_header_text_main = !(ctype_space($header_text_main) || !strlen($header_text_main));
?>
<?php if ($has_header_text_main || is_customize_preview()) :  ?>
<div id="header-text">
  <div>
    <?php echo do_shortcode($header_text_main) ?>
  </div>
</div>
<?php endif ?>