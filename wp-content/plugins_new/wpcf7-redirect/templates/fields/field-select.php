<?php
/**
 * Render a select field
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="field-wrap field-wrap-<?php echo $field['name']; ?> <?php echo isset( $field['class'] ) ? $field['class'] : ''; ?>" >
	<?php if ( $field['label'] ) : ?>
		<label for="wpcf7-redirect-<?php echo $field['name']; ?>">
			<strong><?php echo esc_html( $field['label'] ); ?></strong>
			<?php echo isset( $field['tooltip'] ) ? cf7r_tooltip( $field['tooltip'] ) : ''; ?>
		</label>
	<?php endif; ?>
	<select class="" name="wpcf7-redirect<?php echo $prefix; ?>[<?php echo $field['name']; ?>]">
		<?php foreach ( $field['options'] as $option_key => $option_label ) : ?>
			<?php
				$selected = $option_key ? selected( $field['value'], $option_key, false ) : "selected='selected'";
			?>
			<option value="<?php echo $option_key; ?>" <?php echo $selected; ?>><?php echo $option_label; ?></option>
		<?php endforeach; ?>
	</select>
	<div class="field-footer">
		<?php echo isset( $field['footer'] ) ? $field['footer'] : ''; ?>
	</div>
</div>
