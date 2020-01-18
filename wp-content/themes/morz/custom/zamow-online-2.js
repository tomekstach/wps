jQuery(document).ready(function($) {

  $('.multistep-cf7-next').click(function(e) {

    if ($('#input-nip').val() != '') {
      console.log('Check RODO contract!');

      $('#rodo-name').val($('#input-firma-imie').val() + ' ' + $('#input-firma-nazwisko').val());
      $('#rodo-e-mail').val($('#input-firma-email').val());

      $.ajax({
        url: 'https://wpdev.wapro.pl/erp-service/erp_service.php',
        type: "GET",
        data: {
          nip: $('#input-nip').val()
        }
      }).done(function(string) {
        var obj = JSON.parse(string);

        //console.log(obj.content);

        if (obj.code == 200) {

          if (obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.DataPodpisania) {
            //console.log(obj.content.ArrayDPAgreementGetResult);

            var d = new Date(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.DataPodpisania);
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var output = (day < 10 ? '0' : '') + day + '/' +
              (month < 10 ? '0' : '') + month + '/' +
              d.getFullYear();

            var dataDo = new Date(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.DataDo);
            var current = new Date();

            if (obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.Hosting == '1' && dataDo > current) {
              $('#rodo-rodzaj').val(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.RodzajUmocowania);
              $('#zgoda-rodo').attr('checked', true);
              $('#data-umowy').val(output);
              $('#umowa-podpisana').val('1');
              $('#data-podpisania-umowy').html(output);
              $('#formularz-rodo').css('display', 'none');
              $('#formularz-rodo-info').css('display', 'block');
            } else {
              $('#umowa-podpisana').val('0');
            }
          }
        }
      });
    }
  });

  $('#get-nip-1').click(function(e) {
    if (!ValidateNip($('#input-nip').val(), '#input-nip')) {
      $('.wpcf7-form-control-wrap.NIP .wpcf7-not-valid-tip').remove();
      $('.wpcf7-form-control-wrap.NIP').append('<span role="alert" class="wpcf7-not-valid-tip">NIP jest niepoprawny!</span>');
      return false;
    } else {
      $('.wpcf7-form-control-wrap.NIP .wpcf7-not-valid-tip').remove();
    }

    console.log('Check nip and get nip data!');

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
        $('#input-firma-kod-pocztowy').val(obj.content.postCode);
        $('#input-firma-ulica').val(obj.content.address);
        $('#input-firma-imie').val(obj.content.firstname);
        $('#input-firma-nazwisko').val(obj.content.lastname);
        $('.wpcf7-form-control-wrap.NIP .wpcf7-not-valid-tip').remove();
        $('.firma .wpcf7-not-valid-tip').remove();
        $('.firma-miasto .wpcf7-not-valid-tip').remove();
        $('.firma-kod-pocztowy .wpcf7-not-valid-tip').remove();
        $('.firma-ulica .wpcf7-not-valid-tip').remove();
        $('.firma-imie .wpcf7-not-valid-tip').remove();
        $('.firma-nazwisko .wpcf7-not-valid-tip').remove();
      } else {
        $('.wpcf7-form-control-wrap.NIP .wpcf7-not-valid-tip').remove();
        $('.wpcf7-form-control-wrap.NIP').append('<span role="alert" class="wpcf7-not-valid-tip">' + obj.content + '</span>');
      }
    });
  });

  $('#dane-takie-same input').click(function(e) {

    if ($('#dane-takie-same input').prop('checked')) {
      $('#input-kontakt-imie').val($('#input-firma-imie').val());
      $('#input-kontakt-nazwisko').val($('#input-firma-nazwisko').val());
      $('#input-kontakt-telefon').val($('#input-firma-telefon').val());
      $('#input-kontakt-email').val($('#input-firma-email').val());
    }
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