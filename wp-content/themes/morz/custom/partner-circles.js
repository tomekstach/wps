jQuery(document).ready(function() {
  var szkolenia = parseInt(jQuery('#circle-szkolenia .fl-number-int').html());
  
  if (szkolenia === 0) {
      jQuery('#circle-szkolenia .fl-bar').css('stroke-dashoffset', '408.41px');
      jQuery('#circle-szkolenia .fl-bar').attr('stroke-dashoffset', '408.41');
  } else {
      jQuery('#circle-szkolenia .fl-bar').css('stroke-dashoffset', '0px');
      jQuery('#circle-szkolenia .fl-bar').attr('stroke-dashoffset', '0');
  }
  
  var certyfikaty = parseInt(jQuery('#circle-certyfikaty .fl-number-int').html());
  
  if (certyfikaty === 0) {
      jQuery('#circle-certyfikaty .fl-bar').css('stroke-dashoffset', '408.41px');
      jQuery('#circle-certyfikaty .fl-bar').attr('stroke-dashoffset', '408.41');
  } else {
      jQuery('#circle-certyfikaty .fl-bar').css('stroke-dashoffset', '0px');
      jQuery('#circle-certyfikaty .fl-bar').attr('stroke-dashoffset', '0');
  }
  
  var referencje = parseInt(jQuery('#circle-referencje .fl-number-int').html());
  
  if (referencje === 0) {
      jQuery('#circle-referencje .fl-bar').css('stroke-dashoffset', '408.41px');
      jQuery('#circle-referencje .fl-bar').attr('stroke-dashoffset', '408.41');
  } else {
      jQuery('#circle-referencje .fl-bar').css('stroke-dashoffset', '0px');
      jQuery('#circle-referencje .fl-bar').attr('stroke-dashoffset', '0');
  }
  
  var wdrozenia = parseInt(jQuery('#circle-wdrozenia .fl-number-int').html());
  
  if (wdrozenia === 0) {
      jQuery('#circle-wdrozenia .fl-bar').css('stroke-dashoffset', '408.41px');
      jQuery('#circle-wdrozenia .fl-bar').attr('stroke-dashoffset', '408.41');
  } else {
      jQuery('#circle-wdrozenia .fl-bar').css('stroke-dashoffset', '0px');
      jQuery('#circle-wdrozenia .fl-bar').attr('stroke-dashoffset', '0');
  }
  
  var moduly = parseInt(jQuery('#circle-moduly .fl-number-int').html());
  
  if (moduly === 0) {
      jQuery('#circle-moduly .fl-bar').css('stroke-dashoffset', '408.41px');
      jQuery('#circle-moduly .fl-bar').attr('stroke-dashoffset', '408.41');
  } else {
      jQuery('#circle-moduly .fl-bar').css('stroke-dashoffset', '0px');
      jQuery('#circle-moduly .fl-bar').attr('stroke-dashoffset', '0');
  }
});
