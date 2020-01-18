jQuery(document).ready(function($) {

  $("#password-field").attr("placeholder", "Hasło*");

  $.ajax({
    type: "POST",
    url: "/wp-json/wp/v2/users/me",
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-WP-Nonce', RejestracjaSettings.nonce);
    },
    success: function(data) {
      if (data.id > 0) {
        //console.log(data);

        if (data.roles[0] == 'subscriber') {
          $('#none-form').css('display', 'block');
          $('#form-biuro').css('display', 'none');
        } else {
          $('#user-exist').val(data.id);
          $('#password-field').val(data.name);
          $('#your-login-admin').val(data.name);
          $('#email-admin').val(data.email);
          $('#your-name-admin').val(data.first_name);
          $('#your-lastname-admin').val(data.last_name);
          $('#exist-biuro').css('display', 'block');
          $('#new-biuro').css('display', 'none');
        }
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
      url: 'https://biura.wpdev.wapro.pl/nip-service/checknip.php',
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
        $('#select-wojewodztwo option[value="' + obj.content.state.toLowerCase() + '"]').prop('selected', true);
        $('.your-nip-register .wpcf7-not-valid-tip').remove();
        $('.your-company .wpcf7-not-valid-tip').remove();
        $('.your-adres .wpcf7-not-valid-tip').remove();
        $('.your-code .wpcf7-not-valid-tip').remove();
        $('.your-city .wpcf7-not-valid-tip').remove();
        $('.wojewodztwo .wpcf7-not-valid-tip').remove();
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