jQuery(document).ready(function($) {

  $("#password-field").attr("placeholder", "Hasło*");
  $('#button-edytuj-dane-biura').hide();

  $.ajax({
    type: "POST",
    url: "/wp-json/wl/v1/getBiuroFirm",
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-WP-Nonce', RejestracjaSettings.nonce);
    },
    success: function(data) {
      if (data.ID > 0) {
        console.log(data);
        $('#input-nip').val(data.NIP);
        $('#input-nazwa-firmy').val(data.nazwa_firmy);
        $('#input-email-kontakt').val(data.email);
        $('#input-adres').val(data.adres);
        $('#input-kod-pocztowy').val(data.kod_pocztowy);
        $('#input-miasto').val(data.miasto);
        $('#select-wojewodztwo').val(data.wojewodztwo);
        if (data.testowe_konto == true) {
          $('#input-czy-testowy input').prop('checked', true);
        } else {
          $('#input-czy-testowy input').prop('checked', false);
        }
        $('#input-your-name').val(data.imie);
        $('#input-your-lastname').val(data.nazwisko);
        $('#input-telefon-biuro').val(data.telefon);
        $('#input-fax').val(data.fax);
        $('#input-your-wwww').val(data.www);
        $('#input-your-adres-kospondencja').val(data.k_adres);
        $('#input-your-code-kospondencja').val(data.k_kod_pocztowy);
        $('#input-telefon-kospondencja').val(data.k_telefon);
        $('#input-your-city-kospondencja').val(data.k_miasto);
      }
      if (data.user_id > 0) {
        $('#input-user-exist').val(data.user_id);
      }
      if (data.admin == '1') {
        $('#button-edytuj-dane-biura').show();
      }
    }
  });

  $('#get-nip-1').click(function(e) {
    if (!ValidateNip($('#input-nip').val(), '#input-nip')) {
      $('.your-nip-register .wpcf7-not-valid-tip').remove();
      $('.your-nip-register').append('<span role="alert" class="wpcf7-not-valid-tip">NIP jest niepoprawny!</span>');
      return false;
    } else {
      $('.your-nip-register .wpcf7-not-valid-tip').remove();
    }

    $.ajax({
      url: 'https://biura.wapro.pl/nip-service/checknip.php',
      type: "GET",
      data: {
        nip: $('#input-nip').val()
      }
    }).done(function(string) {
      var obj = JSON.parse(string);
      if (obj.code == 200) {
        $('#input-nazwa-firmy').val(obj.content.name);
        $('#input-miasto').val(obj.content.city);
        $('#input-adres').val(obj.content.address);
        $('#input-kod-pocztowy').val(obj.content.postCode);
        $('#select-wojewodztwo-biuro option[value="' + obj.content.state.toLowerCase() + '"]').prop('selected', true);
        $('.your-nip-register .wpcf7-not-valid-tip').remove();
        $('.your-company .wpcf7-not-valid-tip').remove();
        $('.your-adres .wpcf7-not-valid-tip').remove();
        $('.your-code .wpcf7-not-valid-tip').remove();
        $('.your-city .wpcf7-not-valid-tip').remove();
        $('.wojewodztwo-biuro .wpcf7-not-valid-tip').remove();
      } else {
        $('.your-nip-register .wpcf7-not-valid-tip').remove();
        $('.your-nip-register').append('<span role="alert" class="wpcf7-not-valid-tip">' + obj.content + '</span>');
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