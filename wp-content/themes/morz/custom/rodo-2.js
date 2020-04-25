jQuery(document).ready(function($) {

  $('#rodzaj_umowy').prop('readonly', true);
  $('#rodo-e-mail').prop('readonly', false);

  $('#get-nip-rodo-2').click(function(e) {
    if (!ValidateNip($('#input-nip-klienta').val(), '#input-nip-klienta')) {
      $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
      $('.wpcf7-form-control-wrap.nip-klienta').append('<span role="alert" class="wpcf7-not-valid-tip">NIP jest niepoprawny!</span>');
      return false;
    } else {
      $('.wpcf7-form-control-wrap.nip-klienta .wpcf7-not-valid-tip').remove();
    }

    console.log('Get RODO contract!');

    var contract = getUrlVars()["c"];

    $.ajax({
      type: "POST",
      url: "/wp-json/wl/v1/getRodoContract",
      data: {
        contract: contract,
        nip: $('#input-nip-klienta').val()
      },
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
      },
      success: function(data) {
        $('#rodo-e-mail').prop('readonly', false);
        if (data.umowa_id > 0) {
          console.log('umowa_id: ' + data.umowa_id);

          var d = new Date();
          var month = d.getMonth() + 1;
          var day = d.getDate();
          var output = d.getFullYear() + '-' +
            (month < 10 ? '0' : '') + month + '-' +
            (day < 10 ? '0' : '') + day;

          $("#umowa_id").val(data.umowa_id);
          $("#input-nazwa-firmy-online").val(data.name);
          $("#input-firma-miasto").val(data.city);
          $("#input-firma-kod-pocztowy").val(data.postCode);
          $("#input-firma-ulica").val(data.address);
          $("#serwis-email").val(data.serwis_email);
          $("#rodzaj_umowy").val(data.rodzaj_umowy);
          $("#rodo-e-mail").val(data.email);
          $("#data-podpisania").val(output);
        } else {
          console.log('ERROR!');
          $('.wpcf7-form-control-wrap.nip-klienta').append('<span role="alert" class="wpcf7-not-valid-tip">' + data.error + '</span>');
        }
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

function getUrlVars() {
  var vars = {};
  var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
    function(m, key, value) {
      vars[key] = value;
    });
  return vars;
}