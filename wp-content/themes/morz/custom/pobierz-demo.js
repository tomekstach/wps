jQuery(document).ready(function($) {

  $('#get-nip-1').click(function(e) {
    if (!ValidateNip($('#input-nip').val(), '#input-nip')) {
      $('.yl-nip .wpcf7-not-valid-tip').remove();
      $('.yl-nip').append('<span role="alert" class="wpcf7-not-valid-tip">NIP jest niepoprawny!</span>');
      return false;
    } else {
      $('.yl-nip .wpcf7-not-valid-tip').remove();
    }

    $.ajax({
      url: 'https://wpdev.wapro.pl/nip-service/checknip.php',
      type: "GET",
      data: {
        nip: $('#input-nip').val()
      }
    }).done(function(string) {
      var obj = JSON.parse(string);
      if (obj.code == 200) {
        $('#input-nazwa-firmy').val(obj.content.name);
        $('#input-firma-miasto').val(obj.content.city);
        $('.yl-nip .wpcf7-not-valid-tip').remove();
        $('.textfirma .wpcf7-not-valid-tip').remove();
      } else {
        $('.yl-nip .wpcf7-not-valid-tip').remove();
        $('.yl-nip').append('<span role="alert" class="wpcf7-not-valid-tip">' + obj.content + '</span>');
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