<div class="">
<noscript><h3>It seem that your browser have Javascript disabled: you can't use the chat widget. Enable Javascript on your browser. <a href="<?php echo $w3all_url_to_cms;?>">Visit the forum here</a>.<br /><br /></h3></noscript>
<iframe id="w3all_phpbb_mchat_iframe" name="w3all_phpbb_mchat_iframe" style="width:1px;min-width:100%;*width:100%;border:0;" scrolling="no" src="<?php echo $w3all_url_to_cms . '/app.php/mchat#w3allmchatif'; ?>"></iframe>

<?php 
echo "<script type=\"text/javascript\">
document.domain = '".$document_domain."';
iFrameResize({
				inPageLinks             : true,
        targetOrigin: '".$w3all_url_to_cms."', 
        checkOrigin : '".$document_domain."', // if js error: 'Failed to execute 'postMessage' on 'DOMWindow': The target origin provided does not match the recipient window's origin. Need to fit YOUR domain, ex: mydomain.com
     // heightCalculationMethod: 'documentElementOffset', // If iframe not resize correctly, un-comment (or change with one of others available resize methods) 
     // see: https://github.com/davidjbradshaw/iframe-resizer#heightcalculationmethod
});
</script>";
?>
</div>
