<?php
if($wp_w3all_mchat_shortmode == 1)
{ 
	$w3all_url_to_cms_mchat = ''; // if toogle button
	?>
<div id="w3allmchatshortwrapper" class="w3mchatbox">
<div class="w3mchatbox-inner"><script>var w3all_create_mchattoggleBox = true;</script> <!-- DO NOT remove this - to display/create element button -->
<?php
} else {
	$w3all_url_to_cms_mchat = $w3all_url_to_cms . '/app.php/mchat#w3allmchatif';
	?>
<div id="" class="">
<div class="">	
<?php
}
/*
// substantially instead to open iframe already with src attr, it is set via js when chat toggled->open
// if the shortcode mode is for toggled button
// see /wp-content/plugins/wp-w3all-phpbb-integration/addons/custom_js_css.php
  src="<?php echo $w3all_url_to_cms . '/app.php/mchat#w3allmchatif'; ?>">
*/
?>	
<noscript><h3>It seem that your browser have Javascript disabled: you can't use the chat widget. Enable Javascript on your browser. <a href="<?php echo $w3all_url_to_cms;?>">Visit the forum here</a>.<br /><br /></h3></noscript>
<?php if($wp_w3all_mchat_shortmode == 1) // preloader only for the button toogle
{ ?>
<div id="w3_toogle_wrap_loader" class="w3_no_wrap_loader"><div class="w3_loader"></div></div>
<?php } ?>
<iframe id="w3all_phpbb_mchat_iframe" name="w3all_phpbb_mchat_iframe" style="width:1px;min-width:100%;*width:100%;border:0;" scrolling="no" src="<?php echo $w3all_url_to_cms_mchat; ?>"></iframe>

<?php
echo "<script type=\"text/javascript\">
document.domain = '".$document_domain."';
// document.domain = 'mydomain.com'; // NOTE: reset/setup this with domain if js error when WP is installed like on mysite.domain.com and phpBB on domain.com: js origin error can come out for example when WordPress is on subdomain install and phpBB on domain. The origin fix is needed: (do this also on phpBB overall_footer.html added code)

iFrameResize({
				log                     : false,
				inPageLinks             : true,
        targetOrigin: '".$w3all_url_to_cms."', 
        checkOrigin : '".$document_domain."', // if js error: 'Failed to execute 'postMessage' on 'DOMWindow': The target origin provided does not match the recipient window's origin. Need to fit YOUR domain, ex: mydomain.com
     // heightCalculationMethod: 'documentElementOffset', // If iframe not resize correctly, un-comment (or change with one of others available resize methods) 
     // see: https://github.com/davidjbradshaw/iframe-resizer#heightcalculationmethod
});
</script>";
?>
</div>
</div>