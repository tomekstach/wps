<?php 
/**
* 
*/
class cf7_settings_calculator{
	
	function __construct()
	{
		add_filter("wpcf7_editor_panels",array($this,"custom_form"));
		add_action("wpcf7_save_contact_form", array($this,"save_data"));
	}
	function custom_form($panels)
	{
		$panels["form-panel-calculator-setting"] = array(
				'title' => __( 'Settings calculator', 'contact-form-7' ),
				'callback' => "cf7_calculator_setting_form" );

		return $panels;
	}
	public static function get_settings($post_id  ){
		$cf7_calculator_thousand = (get_post_meta($post_id,"cf7_calculator_thousand",true) ) ? get_post_meta($post_id,"cf7_calculator_thousand",true): ",";
		$cf7_calculator_separator = (get_post_meta($post_id,"cf7_calculator_separator",true) ) ? get_post_meta($post_id,"cf7_calculator_separator",true): ".";
		$cf7_calculator_decimals = (get_post_meta($post_id,"cf7_calculator_decimals",true) != "" ) ? get_post_meta($post_id,"cf7_calculator_decimals",true): "2";
		$cf7_calculator_enable = (get_post_meta($post_id,"cf7_calculator_enable",true) ) ? get_post_meta($post_id,"cf7_calculator_enable",true): "no";
		$cf7_calculator_right = (get_post_meta($post_id,"cf7_calculator_total",true) ) ? get_post_meta($post_id,"cf7_calculator_total",true): "yes";
		$cf7_calculator_currency = (get_post_meta($post_id,"cf7_calculator_currency",true) ) ? get_post_meta($post_id,"cf7_calculator_currency",true): "";
		$cf7_calculator_currency_position = (get_post_meta($post_id,"cf7_calculator_currency_position",true) ) ? get_post_meta($post_id,"cf7_calculator_currency_position",true): "yes";
		return array('enable'=>$cf7_calculator_enable,'decimals' => $cf7_calculator_decimals, 'separator'=>$cf7_calculator_separator,"thousand"=>$cf7_calculator_thousand,"right"=>$cf7_calculator_right,'currency'=>$cf7_calculator_currency,"currency_position"=>$cf7_calculator_currency_position);
	}
	function save_data($contact_form){
        $post_id = $contact_form->id;
        $type = $_POST["cf7_calculator_enable"];
        if( $type !="yes" ) {
        	$type = "no";
        }
        add_post_meta($post_id, 'cf7_calculator_enable', $type,true) or update_post_meta($post_id, 'cf7_calculator_enable', $type);

        $cf7_calculator_thousand = $_POST["cf7_calculator_thousand"];
        add_post_meta($post_id, 'cf7_calculator_thousand', $cf7_calculator_thousand,true) or update_post_meta($post_id, 'cf7_calculator_thousand', $cf7_calculator_thousand);

        $cf7_calculator_separator = $_POST["cf7_calculator_separator"];
        add_post_meta($post_id, 'cf7_calculator_separator', $cf7_calculator_separator,true) or update_post_meta($post_id, 'cf7_calculator_separator', $cf7_calculator_separator);

        $cf7_calculator_decimals = $_POST["cf7_calculator_decimals"];
        add_post_meta($post_id, 'cf7_calculator_decimals', $cf7_calculator_decimals,true) or update_post_meta($post_id, 'cf7_calculator_decimals', $cf7_calculator_decimals);

        $cf7_calculator_total = $_POST["cf7_calculator_total"];
        if( $cf7_calculator_total !="yes" ) {
        	$cf7_calculator_total = "no";
        }
        add_post_meta($post_id, 'cf7_calculator_total', $cf7_calculator_total,true) or update_post_meta($post_id, 'cf7_calculator_total', $cf7_calculator_total);

        $cf7_calculator_currency = $_POST["cf7_calculator_currency"];
        add_post_meta($post_id, 'cf7_calculator_currency', $cf7_calculator_currency,true) or update_post_meta($post_id, 'cf7_calculator_currency', $cf7_calculator_currency);

        $cf7_calculator_currency_position = $_POST["cf7_calculator_currency_position"];
        
        add_post_meta($post_id, 'cf7_calculator_currency_position', $cf7_calculator_currency_position,true) or update_post_meta($post_id, 'cf7_calculator_currency_position', $cf7_calculator_currency_position);
    }
}
new cf7_settings_calculator;
function cf7_calculator_setting_form($post){
    $settings = cf7_settings_calculator::get_settings($post->id);
    ?>
     <table class="form-table">
     	<tr>
			<th scope="row">
				<label for="cf7_calculator_total">
					<?php _e("Total output text align right",CT_7_COST_TEXT_DOMAIN) ?>
				</label>
			</th>
			<td>
				<input <?php checked("yes",$settings["right"]) ?> name="cf7_calculator_total" type="checkbox" value="yes" class="regular-text">
			</td>
		</tr>
        <tr>
			<th scope="row">
				<label for="cf7_calculator_enable">
					<?php _e("Enable format total value",CT_7_COST_TEXT_DOMAIN) ?>
				</label>
			</th>
			<td>
				<input <?php checked("yes",$settings["enable"]) ?> name="cf7_calculator_enable" type="checkbox" value="yes" class="regular-text cf7_calculator_enable">
			</td>
		</tr>
		<?php $class_main = ($settings["enable"] == "yes" )?"cf7_ok":"hidden"; ?>
		<tr class="setting-total-cf7 <?php echo $class_main ?>">
			<th scope="row">
				<label for="cf7_calculator_currency">
					<?php _e("Currency",CT_7_COST_TEXT_DOMAIN) ?>
				</label>
			</th>
			<td>
				<input class="cf7_calculator_currency regular-text" name="cf7_calculator_currency" type="text" value="<?php echo $settings["currency"] ?>" >
			</td>
		</tr>

		<tr class="setting-total-cf7 setting-total-cf7-position <?php echo $class_main ?>">
			<th scope="row">
				<label for="cf7_calculator_currency_position">
					<?php _e("Currency position",CT_7_COST_TEXT_DOMAIN) ?>
				</label>
			</th>
			<td>
				<?php if( $settings["currency"] == "" ){ 
					$settings["currency"] = "$";
					$sub_class ="hidden";
				}else{
					$sub_class ="cf7_ok";
					} ?>
				<select  name="cf7_calculator_currency_position" class="cf7_calculator_currency_position <?php echo $sub_class ?>" >
					<option value="left">Left (<span><?php echo $settings["currency_position"] ?></span>999.99)</option>
					<option <?php selected( $settings["currency_position"], "right" ) ?> value="right">Right (999.99<span><?php echo $settings["currency"] ?></span>)</option>
					<option <?php selected( $settings["currency_position"], "left_space" ) ?>  value="left_space">Left with space (<span><?php echo $settings["currency"] ?></span> 999.99)</option>
					<option <?php selected( $settings["currency_position"], "right_space" ) ?>  value="right_space">Right with space (999.99 <span><?php echo $settings["currency"] ?></span>)</option>
				</select>
			</td>
		</tr>

		<tr class="setting-total-cf7 <?php echo $class_main ?>" >
			<th scope="row">
				<label for="cf7_calculator_thousand">
					<?php _e("Thousand separator",CT_7_COST_TEXT_DOMAIN) ?>
				</label>
			</th>
			<td>
				<input name="cf7_calculator_thousand" type="text" value="<?php echo $settings["thousand"] ?>" class="regular-text">
			</td>
		</tr>
		<tr class="setting-total-cf7 <?php echo $class_main ?>">
			<th scope="row">
				<label for="cf7_calculator_separator">
					<?php _e("Decimal separator",CT_7_COST_TEXT_DOMAIN) ?>
				</label>
			</th>
			<td>
				<input name="cf7_calculator_separator" type="text" value="<?php echo $settings["separator"] ?>" class="regular-text">
			</td>
		</tr>
		<tr class="setting-total-cf7 <?php echo $class_main ?>">
			<th scope="row">
				<label for="cf7_calculator_decimals">
					<?php _e("Number of decimals",CT_7_COST_TEXT_DOMAIN) ?>
				</label>
			</th>
			<td>
				<input name="cf7_calculator_decimals" type="text" value="<?php echo $settings["decimals"] ?>" class="regular-text">
			</td>
		</tr>
        
    </table>
    <?php
}