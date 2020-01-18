jQuery(document).ready(function($) {

  $("input[name='redirect_to']").val(window.location.href);
  console.log('URL: ' + window.location.href);

  var contract = getUrlVars()["contract"];
  var nowa_umowa = getUrlVars()["new"];

  if (nowa_umowa != '' && typeof nowa_umowa !== 'undefined') {
    $("#nowa-umowa").show();
  }

  if (contract != '') {
    $.ajax({
      type: "POST",
      url: "/wp-json/wl/v1/getContract",
      data: {
        contract: contract
      },
      beforeSend: function(xhr) {
        xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
      },
      success: function(data) {

        if (data.umowa_id > 0) {
          console.log('umowa_id: ' + data.umowa_id);
          $("#umowa_id").val(data.umowa_id);
          $("#NIPzlecajacej").val(data.nip);
          $("#zleceniodawca").val(data.nazwa_firmy);
          $("#emailzleceniodawcy").val(data.email);
          $("#first_name").val(data.first_name);
          $("#last_name").val(data.last_name);
          $("#user_tel").val(data.user_tel);
          $('#NIPzlecajacej').prop('readonly', true);
          $('#zleceniodawca').prop('readonly', true);
          $('#emailzleceniodawcy').prop('readonly', true);
          $("#loader-content").hide();
          $("#krok-4-content").show();
        } else {
          console.log('ERROR!');
          $("#loader-content").hide();
          $(".wpcf7-response-output").html(data.message);
          $(".wpcf7-response-output").show();
        }
      }
    });
  }

  var wpcf7Elm = document.querySelector('.wpcf7');

  wpcf7Elm.addEventListener('wpcf7submit', function(event) {

    $("#nowa-umowa").hide();

    if (contract != '') {
      $.ajax({
        type: "POST",
        url: "/wp-json/wl/v1/getContract",
        data: {
          contract: contract
        },
        beforeSend: function(xhr) {
          xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
        },
        success: function(data) {

          if (data.umowa_id > 0) {
            console.log('umowa_id: ' + data.umowa_id);
            $("#umowa_id").val(data.umowa_id);
            $("#NIPzlecajacej").val(data.nip);
            $("#zleceniodawca").val(data.nazwa_firmy);
            $("#emailzleceniodawcy").val(data.email);
            $("#first_name").val(data.first_name);
            $("#last_name").val(data.last_name);
            $("#user_tel").val(data.user_tel);
            $('#NIPzlecajacej').prop('readonly', true);
            $('#zleceniodawca').prop('readonly', true);
            $('#emailzleceniodawcy').prop('readonly', true);
            $("#loader-content").hide();
            $("#krok-4-content").show();
          } else {
            console.log('ERROR!');
            $("#loader-content").hide();
            $(".wpcf7-response-output").html(data.message);
            $(".wpcf7-response-output").show();
          }
        }
      });
    }
  }, false);
});

function getUrlVars() {
  var vars = {};
  var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
    function(m, key, value) {
      vars[key] = value;
    });
  return vars;
}