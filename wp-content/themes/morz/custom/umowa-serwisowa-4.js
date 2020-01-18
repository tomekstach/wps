jQuery(document).ready(function($) {

  $('.cf7-tab.cf7-tab-3 .multistep-cf7-next').click(function(e) {

    if ($('#input-nip').val() != '') {
      console.log('Check RODO contract!');

      $('#NIPzlecajacej').val($('#input-nip').val());
      $('#zleceniodawca').val($('#input-nazwa-firmy').val());
      $('#emailzleceniodawcy').val($('#input-email').val());
      $('#first_name').val($('#input-imie').val());
      $('#last_name').val($('#input-nazwisko').val());
      $('#user_tel').val($('#input-tel').val());
      $('#NIPzlecajacej').prop('readonly', true);
      $('#zleceniodawca').prop('readonly', true);
      $('#emailzleceniodawcy').prop('readonly', true);
    }

    $('.cf7-tab.cf7-tab-4 .multistep-nav-right').html('<p><a class="multistep-cf7-wyslij">Wyślij</a></p>');

    $('.cf7-tab.cf7-tab-4 .multistep-cf7-wyslij').click(function(e) {
      var result = $(this).closest('form').submit();
      return false;
    });
  });

  document.addEventListener('wpcf7mailsent', function(event) {
    //console.log('mailsent!!!');
    location.href = wpcf7_redirect_forms[47037].external_url + '?contract=' + $("#umowa_id").val() + '&new=982y547syrth94w8j0826#nowa-umowa';
  });
});