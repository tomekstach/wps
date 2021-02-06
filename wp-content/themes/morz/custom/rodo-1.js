jQuery(document).ready(function($) {

  //$('#input-nazwa-firmy').prop('readonly', true);

  $('.multistep-cf7-next').click(function(e) {

    if ($('#input-nip').val() != '') {
      console.log('Check RODO contract!');

      $('#rodo-name').val($('#input-firma-imie').val() + ' ' + $('#input-firma-nazwisko').val());
      $('#rodo-e-mail').val($('#input-firma-email').val());

      $.ajax({
        url: 'https://wapro.pl/erp-service/erp_service.php',
        type: "GET",
        data: {
          nip: $('#input-nip').val()
        }
      }).done(function(string) {
        var obj = JSON.parse(string);

        console.log(obj.content);

        if (obj.code == 200) {

          if (Array.isArray(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult)) {
            var DPAgreementGetResult = obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult[obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.length - 1]
          } else {
            var DPAgreementGetResult = obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult;
          }

          if (DPAgreementGetResult.DataPodpisania) {
            console.log(DPAgreementGetResult);

            var d = new Date(DPAgreementGetResult.DataPodpisania);
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var output = (day < 10 ? '0' : '') + day + '/' +
              (month < 10 ? '0' : '') + month + '/' +
              d.getFullYear();

            var dataDo = new Date(DPAgreementGetResult.DataDo);
            var current = new Date();

            if (DPAgreementGetResult.Hosting == '1' && dataDo > current) {
              if (typeof DPAgreementGetResult.RodzajUmocowania !== "undefined" && DPAgreementGetResult.RodzajUmocowania) {
                $('#rodo-rodzaj').val(DPAgreementGetResult.RodzajUmocowania);
              }
              $('#zgoda-rodo').attr('checked', true);
              $('#data-umowy').val(output);
              $('#umowa-podpisana').val('1');
              $('#data-podpisania-umowy').html(output);
              $('#formularz-rodo').css('display', 'none');
              $('#formularz-rodo-info').css('display', 'block');
            } else {
              console.log(DPAgreementGetResult.RodzajUmocowania);
              if (typeof DPAgreementGetResult.RodzajUmocowania !== "undefined" && DPAgreementGetResult.RodzajUmocowania) {
                $('#rodo-rodzaj').val(DPAgreementGetResult.RodzajUmocowania);
              }
              $('#umowa-podpisana').val('0');
            }
          }
        }
      });
    }
  });

  $('#get-nip-klienta').click(function(e) {
    if (!ValidateNip($('#input-nip-klienta').val(), '#input-nip-klienta')) {
      $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
      $('.wpcf7-form-control-wrap.nip-klienta').append('<span role="alert" class="wpcf7-not-valid-tip">NIP jest niepoprawny!</span>');
      return false;
    } else {
      $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
    }

    console.log('Check nip and get nip data 2!');

    $.ajax({
      url: 'https://wapro.pl/erp-service/erp_service.php',
      type: "GET",
      data: {
        nip: $('#input-nip-klienta').val(),
        check: 5
      }
    }).done(function(string) {
      var obj = JSON.parse(string);
      console.log(obj);
      if (obj.code == 200) {
        if (obj.content.ArrayCustomerGetResult.Status == 1) {
          if (obj.content.ArrayCustomerGetResult.CustomerGetResult.Nazwa2) {
            console.log('2');
            $('#input-nazwa-firmy-klienta').val(obj.content.ArrayCustomerGetResult.CustomerGetResult.Nazwa2);
          } else {
            console.log('1');
            $('#input-nazwa-firmy-klienta').val(obj.content.ArrayCustomerGetResult.CustomerGetResult.Nazwa1);
          }

          var message = '<h5>Klient istnieje w ERPie</h5>';

          if (obj.responseAgreement.ArrayDPAgreementGetResult.Status == 1) {
            if (Array.isArray(obj.responseAgreement.ArrayDPAgreementGetResult.DPAgreementGetResult)) {
              var DPAgreementGetResult = obj.responseAgreement.ArrayDPAgreementGetResult.DPAgreementGetResult[obj.responseAgreement.ArrayDPAgreementGetResult.DPAgreementGetResult.length - 1]
            } else {
              var DPAgreementGetResult = obj.responseAgreement.ArrayDPAgreementGetResult.DPAgreementGetResult;
            }

            if (DPAgreementGetResult.DataPodpisania) {
              message += '<p>Klient ma podpisaną umowę RODO (' + DPAgreementGetResult.DataPodpisania.replace('T', ' ') + ') na:</p><ul>';
            } else {
              message += '<p style="color: red;">Klient ma umowę RODO (umowa nie jest podpisana) na:</p><ul>';
            }
            if (DPAgreementGetResult.Hosting == 1) {
              message += '<li>Hosting</li>';
            }
            if (DPAgreementGetResult.UruchTestProg == 1) {
              message += '<li>Uruchomienie lub testowanie programu na danych rzeczywistych</li>';
            }
            if (DPAgreementGetResult.Outsourcing == 1) {
              message += '<li>Outsourcing</li>';
            }
            if (DPAgreementGetResult.Konserwacja == 1) {
              message += '<li>Konserwacja</li>';
            }

            message += '</ul>';
          } else {
            message += '<h5>Klient NIE MA podpisanej żadnej umowy RODO</k5>';
          }

          $('#klient-info').html(message);
          $('#klient-info').show();
        } else {
          var message = '<h5>Klient nie istnieje w ERPie</h5>';
          $('#klient-info').html(message);
          $('#klient-info').show();
          $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
          $('.wpcf7-form-control-wrap.nip-klienta').append('<span role="alert" class="wpcf7-not-valid-tip">' + obj.content.ArrayCustomerGetResult.ErrorMessage + '</span>');
        }
      } else {
        $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
        $('.wpcf7-form-control-wrap.nip-klienta').append('<span role="alert" class="wpcf7-not-valid-tip">' + obj.content + '</span>');
      }
    });

    $.ajax({
      url: 'https://wapro.pl/nip-service/checknip.php',
      type: "GET",
      data: {
        nip: $('#input-nip-klienta').val()
      }
    }).done(function(string) {
      var obj = JSON.parse(string);

      if (obj.code == 200) {
        $('#input-firma-miasto').val(obj.content.city);
        $('#input-firma-kod-pocztowy').val(obj.content.postCode);
        $('#input-firma-ulica').val(obj.content.address);
        $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
        $('.firma-miasto .wpcf7-not-valid-tip').remove();
        $('.firma-kod-pocztowy .wpcf7-not-valid-tip').remove();
        $('.firma-ulica .wpcf7-not-valid-tip').remove();
      } else {
        $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
        $('.wpcf7-form-control-wrap.nip-klienta').append('<span role="alert" class="wpcf7-not-valid-tip">' + obj.content + '</span>');
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