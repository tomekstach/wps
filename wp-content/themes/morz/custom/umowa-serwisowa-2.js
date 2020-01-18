jQuery(document).ready(function($) {

  $('.cf7-tab-1 .multistep-cf7-next').click(function(e) {

    $("#loader-content").show();
    $("#rodo-content").hide();

    // Remove all characters which are not digits
    var nipVal = $('#input-nip').val();
    $('#input-nip').val(nipVal.replace(/[^0-9]/g, ""));
    console.log("NIP: " + $('#input-nip').val());

    if ($('#input-nip').val() != '') {
      console.log('Check RODO contract!');

      $('#rodo-name').val($('#input-imie').val() + ' ' + $('#input-nazwisko').val());
      $('#rodo-e-mail').val($('#input-email').val());

      $.ajax({
        url: 'https://pomoc.wpdev.wapro.pl/erp-service/erp_service.php',
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

            var d = new Date(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.DataPodpisania);
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var output = (day < 10 ? '0' : '') + day + '/' +
              (month < 10 ? '0' : '') + month + '/' +
              d.getFullYear();

            var dataDo = new Date(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.DataDo);
            var current = new Date();

            if (obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.UruchTestProg == '1' && dataDo > current) {
              $('#rodo-rodzaj').val(obj.content.ArrayDPAgreementGetResult.DPAgreementGetResult.RodzajUmocowania);
              $('#zgoda-rodo').attr('checked', true);
              $('#data-umowy').val(output);
              $('#umowa-podpisana').val('1');
              $('#data-podpisania-umowy').html(output);
              $('#formularz-rodo').css('display', 'none');
              $('#formularz-rodo-info').css('display', 'block');
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
    console.log('Send RODO contract!');

    if ($('#umowa_id').val() == '') {
      $.ajax({
        type: "POST",
        url: "/wp-json/wl/v1/addContract",
        data: {
          nip: $('#input-nip').val(),
          firm: $('#input-nazwa-firmy').val(),
          email: $('#input-email').val(),
          firstname: $('#input-imie').val(),
          lastname: $('#input-nazwisko').val(),
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