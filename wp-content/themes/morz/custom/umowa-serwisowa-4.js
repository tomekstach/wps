jQuery(document).ready(function($) {

  $('.program select').change(function() {
    $('#cf7-container-builder-1559219370795 select').val("wybierz numer wersji");
    $('#cf7-container-builder-1559219370795 option').removeAttr('selected');
  });

  $('.cf7-tab.cf7-tab-3 .multistep-cf7-next').click(function(e) {

    if ($('#input-nip').val() != '') {
      console.log('Check RODO contract!');

      $('#NIPzlecajacej').val($('#input-nip').val());
      $('#zleceniodawca').val($('#input-nazwa-firmy').val());
      $('#emailzleceniodawcy').val($('#input-email').val());
      $('#first_name').val($('#input-imie-klient').val());
      $('#last_name').val($('#input-nazwisko-klient').val());
      $('#user_tel').val($('#input-tel').val());
      $('#NIPzlecajacej').prop('readonly', true);
      $('#zleceniodawca').prop('readonly', true);
      $('#emailzleceniodawcy').prop('readonly', true);
    }

    /*$('.cf7-tab.cf7-tab-4 .multistep-nav-right').html('<p><a class="multistep-cf7-wyslij">Wyślij</a><span class="ajax-loader"></span></p>');

    $('.cf7-tab.cf7-tab-4 .multistep-cf7-wyslij').click(function(e) {
      var result = $(this).closest('form').submit();
      return false;
    });*/
  });

  /*document.addEventListener('wpcf7mailsent', function(event) {
    console.log('mailsent!!!');
    location.href = wpcf7_redirect_forms[47037].external_url + '?contract=' + $("#umowa_id").val() + '&new=982y547syrth94w8j0826#nowa-umowa';
  });*/
});