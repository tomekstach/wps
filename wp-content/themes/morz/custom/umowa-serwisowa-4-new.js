jQuery(document).ready(function($) {

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
  });
});