<?php

/**
 * Frontend Reset Password - Main lost password form
 * 
 * @version	1.0.5
 */

if (!defined('ABSPATH')) {
  exit;
}

?>

<div id="password-lost-form-wrap">

  <?php if (!empty($errors)) : ?>

  <?php foreach ($errors as $error) : ?>
  <p class="som-password-sent-message som-password-error-message">
    <span><?php echo $error; ?></span>
  </p>
  <?php endforeach; ?>

  <?php endif; ?>
  <?php
  /*
	echo '<p>' . $url . '</p>';
*/
  ?>
  <?php $email_confirmed = isset($_REQUEST['email_confirmed']) ? $_REQUEST['email_confirmed'] : '';

  if ($email_confirmed) : ?>
  <p class="som-password-sent-message">
    <i class="fa fa-check" aria-hidden="true"></i><span><?php echo $email_confirmed; ?></span>
  </p>
  <?php endif; ?>

  <form id="lostpasswordform" method="post" class="account-page-form">
    <fieldset>

      <div class="somfrp-lost-pass-form-text">
        <?php echo $lost_text_output; ?>
      </div>

      <p class="no-margin">
        <label for="email"><?php _e('Email Address or Username', 'frontend-reset-password'); ?></label>
        <input type="text" name="somfrp_user_info" id="somfrp_user_info">
      </p>

      <div class="lostpassword-submit">
        <?php wp_nonce_field('somfrp_lost_pass', 'somfrp_nonce'); ?>
        <input type="hidden" name="submitted" id="submitted" value="true">
        <input type="hidden" name="somfrp_action" id="somfrp_post_action" value="somfrp_lost_pass">
        <button type="submit" id="reset-pass-submit" name="reset-pass-submit"
          class="button big-btn"><?php echo $button_text; ?></button>
      </div>

    </fieldset>
  </form>

</div>