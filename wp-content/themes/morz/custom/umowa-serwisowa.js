jQuery(document).ready(function($) {

  /*if (!$('.cf7-steps-1 .fl-icon-wrap').length) {
    $('.cf7-steps-1').prepend('<span class="fl-icon-wrap"><span class="fl-icon"><i class="ssui-icon ssui-icon--Zasb-291"></i><br></span></span>');
  }

  if (!$('.cf7-steps-2 .fl-icon-wrap').length) {
    $('.cf7-steps-2').prepend('<span class="fl-icon-wrap"><span class="fl-icon"><i class="ssui-icon ssui-icon--Zasb-290"></i><br></span></span>');
  }

  if (!$('.cf7-steps-3 .fl-icon-wrap').length) {
    $('.cf7-steps-3').prepend('<span class="fl-icon-wrap"><span class="fl-icon"><i class="ssui-icon ssui-icon--Zasb-289"></i><br></span></span>');
  }

  if (!$('.cf7-steps-4 .fl-icon-wrap').length) {
    $('.cf7-steps-4').prepend('<span class="fl-icon-wrap"><span class="fl-icon"><i class="ssui-icon ssui-icon--Zasb-293"></i><br></span></span>');
  }*/

  $.ajax({
    type: "POST",
    url: "/wp-json/wl/v1/user",
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
    },
    success: function(data) {

      if (data.id > 0) {
        //console.log(data);
        $('#input-nazwa-firmy').val(data.user_firm);
        $('#input-nip').val(data.user_nip);
        $('#input-imie').val(data.first_name);
        $('#input-nazwisko').val(data.last_name);
        $('#input-tel').val(data.user_tel);
        $('#input-email').val(data.user_email);
      }
    }
  });

  $('#get-nip-1').click(function(e) {
    if (!ValidateNip($('#input-nip').val(), '#input-nip')) {
      $('.numbernip .wpcf7-not-valid-tip').remove();
      $('.numbernip').append('<span role="alert" class="wpcf7-not-valid-tip">NIP jest niepoprawny!</span>');
      return false;
    } else {
      $('.numbernip .wpcf7-not-valid-tip').remove();
    }

    $.ajax({
      url: 'https://pomoc.wpdev.wapro.pl/nip-service/checknip.php',
      type: "GET",
      data: {
        nip: $('#input-nip').val()
      }
    }).done(function(string) {
      var obj = JSON.parse(string);
      if (obj.code == 200) {
        $('#input-nazwa-firmy').val(obj.content.name);
        //$('#input-imie').val(obj.content.firstname);
        //$('#input-nazwisko').val(obj.content.lastname);
        $('.numbernip .wpcf7-not-valid-tip').remove();
        $('.textfirma .wpcf7-not-valid-tip').remove();
        //$('.imie .wpcf7-not-valid-tip').remove();
        //$('.nazwisko .wpcf7-not-valid-tip').remove();
      } else {
        $('.numbernip .wpcf7-not-valid-tip').remove();
        $('.numbernip').append('<span role="alert" class="wpcf7-not-valid-tip">' + obj.content + '</span>');
      }
    });
  });
});

function ValidateNip(nip, iden) {
  nip = nip.replace(/[\ \-]/gi, '');

  jQuery(iden).val(nip);

  if (/^([0-9])\1{9}$/.test(nip)) {
    return false;
  }

  var weight = [6, 5, 7, 2, 3, 4, 5, 6, 7];
  var sum = 0;
  var controlNumber = parseInt(nip.substring(9, 10));
  for (let i = 0; i < weight.length; i++) {
    sum += (parseInt(nip.substring(i, i + 1)) * weight[i]);
  }

  return sum % 11 === controlNumber;
}