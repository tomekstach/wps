jQuery(document).ready(function($) {
	
	
	$(".cf7_calculator_currency").change(function(event) {
		/* Act on the event */
		if($(this).val() == "" ){
            $(".cf7_calculator_currency_position").addClass('hidden');
		}else{
            $(".cf7_calculator_currency_position").removeClass('hidden');
		}
	});
	$(".cf7_calculator_enable").change(function(event) {
		$(".setting-total-cf7").toggleClass('hidden');
	});
	$(".cf7_import_demo_calculator").click(function(event) {
		/* Act on the event */
		event.preventDefault();
		if (confirm('It will overwrite the current content! Do you want to do it?')) {
		    $("#wpcf7-form").val(cf7_calculator.data);
		    $("#contact-form-editor-tabs li").first().find('a').click();
		} 
	});
});