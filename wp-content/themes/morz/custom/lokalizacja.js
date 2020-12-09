jQuery(document).ready(function($) {
  $('#loc-error').hide();
  var loc = getUrlVars()["loc"];

  $.ajax({
    type: "POST",
    url: "/wp-json/wl/v1/getLocation",
    data: {
      loc: loc
    },
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
    },
    success: function(data) {
      console.log(data);
      if (data == false) {
        $('#loc-error').show();
        $('#loc-error-info').html('<h2>Błąd!</h2><p>Nie masz uprawnień do tej lokalizacji!</p>');
        $('#loc-name').hide();
        $('#loc-main-data').hide();
        $('#loc-staff').hide();
        $('#loc-clients').hide();
      } else {
        $('#loc-name h1').html(data.post_title);

        if (data.admin == '1') {
          console.log('I can edit this location!');
          $('#location_ID').val(data.ID);
          $('#location_ID_staff').val(data.ID);
          $('#location_ID_client').val(data.ID);
          $('#input-nazwa-firmy').val(data.post_title);
          $('#input-adres').val(data.adres);
          $('#input-kod-pocztowy').val(data.kod_pocztowy);
          $('#input-miasto').val(data.miasto);
          $('#select-wojewodztwo').val(data.wojewodztwo);
          $('#input-fax').val(data.fax);
          $('#input-telefon').val(data.telefon);
          $('#input-www').val(data.www);
          $('#input-email').val(data.email);

          if (data.rodzaj_biura.length > 0) {
            for (var i = 0; i < data.rodzaj_biura.length; i++) {
              $(':input[value=\'' + data.rodzaj_biura[i] + '\']').prop("checked", true);
            }
          }

          if (data.obszar_dzialania.length > 0) {
            for (var i = 0; i < data.obszar_dzialania.length; i++) {
              $(':input[value=\'' + data.obszar_dzialania[i] + '\']').prop("checked", true);
            }
          }

          if (data.zakres_uslug.length > 0) {
            for (var i = 0; i < data.zakres_uslug.length; i++) {
              $(':input[value=\'' + data.zakres_uslug[i] + '\']').prop("checked", true);
            }
          }

          if (data.zakres_uslug_online.length > 0) {
            for (var i = 0; i < data.zakres_uslug_online.length; i++) {
              $(':input[value=\'' + data.zakres_uslug_online[i] + '\']').prop("checked", true);
            }
          }

          if (data.online.length > 0) {
            if (data.online[0] == 'tak') {
              $(':input[name=zgloszenie-biura]').prop("checked", true);
            }
          }
        } else {
          $('#loc-main-data .pp-modal-button').hide();
          $('#loc-staff .pp-modal-button').hide();
          $('#loc-clients .pp-modal-button').hide();
        }

        var workersLength = data.workers.length;
        var html = "";

        if (workersLength > 0) {
          for (var i = 0; i < workersLength; i++) {
            //console.log(data[i]);
            html += '<tr class="pp-table-row odd">';
            html += '<td>' + data.workers[i].first_name + ' ' + data.workers[i].last_name + '</td>';
            html += '<td>' + data.workers[i].username + '</td>';
            html += '<td>' + data.workers[i].email + '</td>';
            html += '<td></td>';
            html += '<td></td>';
            html += "</tr>";
          }
        }

        $("#lista-pracownikow tbody").html(html);

        var clientsLength = data.clients.length;
        var html = "";

        if (clientsLength > 0) {
          for (var i = 0; i < clientsLength; i++) {
            //console.log(data[i]);
            html += '<tr class="pp-table-row odd">';
            html += '<td>' + data.clients[i].nazwa_firmy + '</td>';
            html += '<td>' + data.clients[i].nip + '</td>';
            html += '<td>' + data.clients[i].email + '</td>';
            html += '<td>' + data.clients[i].adres + '</td>';
            html += '<td>' + data.clients[i].kod_pocztowy + '</td>';
            html += '<td>' + data.clients[i].miasto + '</td>';
            html += '<td>' + data.clients[i].wojewodztwo + '</td>';
            html += '<td></td>';
            html += '<td></td>';
            html += "</tr>";
          }
        }

        $("#lista-klientow tbody").html(html);

        if (data.user_id > 0) {
          $('#addloc-user-exist').val(data.user_id);
        }
      }
    }
  });

  $('#get-nip-2').click(function(e) {
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
        $('#input-nazwa-firmy-cli').val(obj.content.name);
        $('#input-miasto-cli').val(obj.content.city);
        $('#input-adres-cli').val(obj.content.address);
        $('#input-kod-pocztowy-cli').val(obj.content.postCode);
        $('#select-wojewodztwo-cli option[value="' + obj.content.state.toLowerCase() + '"]').prop('selected', true);
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

function getUrlVars() {
  var vars = {};
  var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
    function(m, key, value) {
      vars[key] = value;
    });
  return vars;
}