<?php
include CT_7_COST_PLUGIN_PATH."backend/settings.php";
include CT_7_COST_PLUGIN_PATH."backend/checkbox.php";
include CT_7_COST_PLUGIN_PATH."backend/select.php";
include CT_7_COST_PLUGIN_PATH."backend/demo.php";
class cf7_cost_calculator_backend{
	function __construct(  ){
		add_action( 'wpcf7_admin_init' , array($this,'add_tag_generator_total') , 100 );
		add_action( 'wpcf7_init', array($this,'add_shortcode_total') , 20 );
		add_action("admin_enqueue_scripts",array($this,"add_lib"),0,0);
		add_filter( 'wpcf7_validate_calculated', array($this,'wpcf7_calculated_validation_filter'), 10, 2 );
		add_filter( 'wpcf7_validate_calculated*', array($this,'wpcf7_calculated_validation_filter'), 10, 2 );
	}
	function wpcf7_calculated_validation_filter( $result, $tag ) {
		$name = $tag->name;

		$value = isset( $_POST[$name] )
			? trim( strtr( (string) $_POST[$name], "\n", " " ) )
			: '';

		$min = $tag->get_option( 'min', 'signed_int', true );
		$max = $tag->get_option( 'max', 'signed_int', true );


		if ( $tag->is_required() && '' == $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'invalid_required' ) );
		}  elseif ( '' != $value && '' != $min && (float) $value < (float) $min ) {
			$result->invalidate( $tag, wpcf7_get_message( 'number_too_small' ) );
		} elseif ( '' != $value && '' != $max && (float) $max < (float) $value ) {
			$result->invalidate( $tag, wpcf7_get_message( 'number_too_large' ) );
		}

		return $result;
	}
	function add_lib(){
        wp_enqueue_script("cf7_calculator",CT_7_COST_PLUGIN_URL."backend/js/cf7_calculator.js",array("jquery"),time());
        $demo = '<label> Fieldname 1 *
  [number* fieldname1 "1"]
</label>

<label> Fieldname 2 (required)
  [checkbox_custom fieldname2 default:0 use_label_element "10|Blue $10" "15|Red $15" "50|Yellow $50"]
</label>

<label> Size (Fieldname 3)
  [radio_custom fieldname3 default:1  use_label_element "10|S $10" "15|L $15" "50|XL $50"]
</label>

<label> Quantity (Fieldname 4)
[select_custom fieldname4 "1|1" "2|2" "3|3"]
</label>

<label> Total 1( (fieldname1 + fieldname2 + fieldname3) * fieldname4 )
  [calculated calculated-326 "(fieldname1 + fieldname2 + fieldname3) * fieldname4"]
</label>

<label> Total 2( ((fieldname1 + fieldname2 + fieldname3) * fieldname4)/2 )
  [calculated calculated-327 "((fieldname1 + fieldname2 + fieldname3) * fieldname4)/2"]
</label>

<label> Total 3 - Hide input and show label ( ((fieldname1 + fieldname2 + fieldname3) * fieldname4) * 10000 )
  [calculated calculated-3288 cf7-label "((fieldname1 + fieldname2 + fieldname3) * fieldname4)*10000 "]
</label>

<label> Your Name (required)
    [text* your-name] </label>

<label> Your Email (required)
    [email* your-email] </label>

<label> Subject
    [text your-subject] </label>

<label> Your Message
    [textarea your-message] </label>

[submit "Send"]';
        wp_localize_script( "cf7_calculator", "cf7_calculator", array("data"=>$demo) );
    }
	function add_tag_generator_total(){
		if ( ! class_exists( 'WPCF7_TagGenerator' ) ) return;

		$tag_generator = WPCF7_TagGenerator::get_instance();
		$tag_generator->add( 'calculated', __( 'Calculated', 'contact-form-7' ),
			array($this,'tag_generator_total') );

	}
	function tag_generator_total($contact_form , $args = ''){
		$args = wp_parse_args( $args, array() );
		$type = $args['id'];
		?>
		<div class="control-box">
			<fieldset>
				<table class="form-table">
					<tbody>
						<tr>
						<th scope="row"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></th>
						<td>
							<fieldset>
							<legend class="screen-reader-text"><?php echo esc_html( __( 'Field type', 'contact-form-7' ) ); ?></legend>
							<label><input type="checkbox" name="required" /> <?php echo esc_html( __( 'Required field', 'contact-form-7' ) ); ?></label>
							</fieldset>
						</td>
						</tr>
						<tr>
							<th scope="row"><?php echo esc_html( __( 'Range', 'contact-form-7' ) ); ?></th>
							<td>
								<fieldset>
								<legend class="screen-reader-text"><?php echo esc_html( __( 'Range', 'contact-form-7' ) ); ?></legend>
								<label>
								<?php echo esc_html( __( 'Min', 'contact-form-7' ) ); ?>
								<input type="number" name="min" class="numeric option" />
								</label>
								&ndash;
								<label>
								<?php echo esc_html( __( 'Max', 'contact-form-7' ) ); ?>
								<input type="number" name="max" class="numeric option" />
								</label>
								</fieldset>
							</td>
							</tr>
	
						<tr>
							<th scope="row">Type input</th>
							<td><label><input type="checkbox" name="cf7-label" class="option" value="on"> Hide input and show lable</label></td>
						</tr>

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-name' ); ?>"><?php echo esc_html( __( 'Name', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="name" class="tg-name oneline" id="<?php echo esc_attr( $args['content'] . '-name' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
						</tr>

						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'contact-form-7' ) ); ?></label></th>
							<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row">Hide Field</th>
							<td><label><input type="checkbox" name="cf7-hide" class="option" value="on"> Hide field</label></td>
						</tr>
						<tr>
							<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Set Equation', 'contact-form-7' ) ); ?></label></th>
							<td>
								<?php $tags = $contact_form->scan_form_tags();
									$all_tags_name = array();
				                    foreach ($tags as $tag_inner):
				                        
				                		$all_tags_name[]=$tag_inner['name'];
				                    endforeach;
				                  ?>
								<p class="cf7-container-tags">
									<?php echo "<strong>".implode('</strong> <strong>', $all_tags_name)."</strong>"; ?>

								</p>
								<textarea rows="3" class="large-text code" name="values" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>"></textarea> <br>
								<?php _e( 'Eg: (number-253 + number-254)/ 2 + radio_custom-708 + checkbox_custom-708', 'contact-form-7' ); ?> <br>
									<strong>number-253, number-254, radio_custom-708, checkbox_custom-708</strong> is name field
								</td>
							</tr>
						</tbody>
					</table>
				</fieldset>
			</div>

			<div class="insert-box">
				<input type="text" name="<?php echo $type; ?>" class="tag code" readonly="readonly" onfocus="this.select()" />

				<div class="submitbox">
					<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'contact-form-7' ) ); ?>" />
				</div>

				<br class="clear" />

			</div>
			<?php
		}
		function add_shortcode_total(){
			wpcf7_add_form_tag(
				array( 'calculated' , 'calculated*'  ),
				array($this,'total_shortcode_handler'), true );
		}
		function total_shortcode_handler( $tag ) {
			//var_dump($tag);
			$tag = new WPCF7_FormTag( $tag );
			if ( empty( $tag->name ) )
				return '';
			//var_dump($tag);
			$validation_error = wpcf7_get_validation_error( $tag->name );

			$class = wpcf7_form_controls_class( $tag->type, 'wpcf7-total' );


			if ( $validation_error )
				$class .= ' wpcf7-not-valid';

			$atts = array();

			$atts['size'] = $tag->get_size_option( '40' );
			$atts['maxlength'] = $tag->get_maxlength_option();
			$atts['minlength'] = $tag->get_minlength_option();
			$atts['min'] = $tag->get_option( 'min', 'signed_int', true );
			$atts['max'] = $tag->get_option( 'max', 'signed_int', true );

			if ( $atts['maxlength'] && $atts['minlength'] && $atts['maxlength'] < $atts['minlength'] ) {
				unset( $atts['maxlength'], $atts['minlength'] );
			}

			$atts['class'] = $tag->get_class_option( $class );
			$cf_lable = false;
			if( is_array($tag["options"]) ) {
				foreach ($tag["options"] as $vl) {
					if( $vl=="cf7-hide" ) {
						$atts['class'] .=" cf7-hide";
					}elseif ( $vl == "cf7-label") {
						$cf_lable = true;
					}
				}
			}
			

			$atts['class'] .= " ctf7-total";
			$atts['id'] = $tag->get_id_option();
			$atts['tabindex'] = $tag->get_option( 'tabindex', 'int', true );


			$atts['readonly'] = 'readonly';


			if ( $tag->is_required() )
				$atts['aria-required'] = 'true';

			$atts['aria-invalid'] = $validation_error ? 'true' : 'false';

			$value = (string) reset( $tag->values );


			if ( $tag->has_option( 'placeholder' ) || $tag->has_option( 'watermark' ) ) {
				$atts['placeholder'] = $value;
				$value = '';
			}

			$value = $tag->get_default_option( $value );

			$value = wpcf7_get_hangover( $tag->name, $value );

			$scval = do_shortcode('['.$value.']');
			if( $scval != '['.$value.']' ){
				$value = esc_attr( $scval );
			}
			$atts['value'] = 0;

			$atts['type'] = 'text';

			$atts['name'] = $tag->name;

			$atts = wpcf7_format_atts( $atts );
			if( $cf_lable ) {
				$custom = '<strong class="cf7-calculated-name">0</strong>';
				$html = sprintf(
				'<span class="wpcf7-form-control-wrap %1$s">%5$s <input data-number="0" style="display:none;" %2$s %4$s />%3$s</span>',
				sanitize_html_class( $tag->name ), $atts, $validation_error, 'data-formulas="'.$value.'"',$custom );
			}else{
				$html = sprintf(
				'<span class="wpcf7-form-control-wrap %1$s"><input data-number="0" %2$s %4$s />%3$s</span>',
				sanitize_html_class( $tag->name ), $atts, $validation_error, 'data-formulas="'.$value.'"' );
			}
			

			return $html;
		}
	}

	new cf7_cost_calculator_backend;