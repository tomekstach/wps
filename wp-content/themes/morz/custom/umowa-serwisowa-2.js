jQuery(document).ready(function($) {

  $('.cf7-tab-1 .multistep-cf7-next').click(function(e) {

    $("#loader-content").show();
    $("#rodo-content").hide();
    $('.cf7-tab-1 .multistep-cf7-next').removeClass('hidden');

    // Remove all characters which are not digits
    var nipVal = $('#input-nip').val();
    $('#input-nip').val(nipVal.replace(/[^0-9]/g, ""));
    console.log("NIP: " + $('#input-nip').val());

    if ($('#input-nip').val() != '') {
      console.log('Check RODO contract!');

      $('#rodo-name').val($('#input-imie-klient').val() + ' ' + $('#input-nazwisko-klient').val());
      $('#rodo-e-mail').val($('#input-email').val());
      $('#rodo-name').prop('readonly', true);
      $('#rodo-e-mail').prop('readonly', true);

      $.ajax({
        url: 'https://pomoc.wapro.pl/erp-service/erp_service.php',
        type: "GET",
        data: {
          nip: $('#input-nip').val()
        }
      }).done(function(string) {
        var obj = JSON.parse(string);

        //console.log(obj.content);

        var c_d = new Date();
        var c_month = c_d.getMonth() + 1;
        var c_day = c_d.getDate();
        var c_output = (c_day < 10 ? '0' : '') + c_day + '/' +
          (c_month < 10 ? '0' : '') + c_month + '/' +
          c_d.getFullYear();

        if (obj.code == 200) {

          if (obj.content.ArrayDPAgreementGetResult.Status == '1') {
            console.log(obj.content.ArrayDPAgreementGetResult);

            if (Array.isArray(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult)) {
              var DPAgreementGetResult = obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult[obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.length - 1]
            } else {
              var DPAgreementGetResult = obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult;
            }

            var d = new Date(DPAgreementGetResult.DataPodpisania);
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var output = (day < 10 ? '0' : '') + day + '/' +
              (month < 10 ? '0' : '') + month + '/' +
              d.getFullYear();

            var dataDo = new Date(DPAgreementGetResult.DataDo);
            var current = new Date();

            if (DPAgreementGetResult.UruchTestProg == '1' && dataDo > current) {
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
              if (typeof DPAgreementGetResult.RodzajUmocowania !== "undefined" && DPAgreementGetResult.RodzajUmocowania) {
                $('#rodo-rodzaj').val(DPAgreementGetResult.RodzajUmocowania);
              }
              $('#formularz-rodo').css('display', 'block');
              $('#formularz-rodo-info').css('display', 'none');
              $('#data-umowy').val(c_output);
              $('#data-podpisania-umowy').html(c_output);
              $('#umowa-podpisana').val('0');
            }
          } else {
            $('#formularz-rodo').css('display', 'block');
            $('#formularz-rodo-info').css('display', 'none');
            $('#data-umowy').val(c_output);
            $('#data-podpisania-umowy').html(c_output);
            $('#umowa-podpisana').val('0');
          }
        } else {
          $('#formularz-rodo').css('display', 'block');
          $('#formularz-rodo-info').css('display', 'none');
          $('#data-umowy').val(c_output);
          $('#data-podpisania-umowy').html(c_output);
          $('#umowa-podpisana').val('0');
        }

        $("#loader-content").hide();
        $("#rodo-content").show();
      });
    }
  });

  $('.cf7-tab-2 .multistep-cf7-next').click(function(e) {

    if ($('#umowa_id').val() == '' && $('#input-imie-klient').val() != '' && $('#input-nazwisko-klient').val() != '' && $('#rodo-rodzaj').val() != '' && $('#zgoda-rodo').prop('checked')) {
      console.log('Send RODO contract!');
      $.ajax({
        type: "POST",
        url: "/wp-json/wl/v1/addContract",
        data: {
          nip: $('#input-nip').val(),
          firm: $('#input-nazwa-firmy').val(),
          email: $('#input-email').val(),
          firstname: $('#input-imie-klient').val(),
          lastname: $('#input-nazwisko-klient').val(),
          phone: $('#input-tel').val(),
          rodoRodaj: $('#rodo-rodzaj').val(),
          umowaPodpisana: $('#umowa-podpisana').val()
        },
        beforeSend: function(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
        },
        success: function(umowa_id) {

          if (umowa_id > 0) {
            console.log('umowa_id: ' + umowa_id);
            $('#umowa_id').val(umowa_id);
          }
        }
      });
    }
  });
});