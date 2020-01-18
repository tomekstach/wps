jQuery(document).ready(function($) {

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
          $('#register-is-biuro').css('display', 'block');
          $('#register-not-biuro').css('display', 'none');
          $('#register-dalej').css('display', 'none');
          $('#register-wyloguj').css('display', 'none');
        } else {
          $('#register-is-biuro').css('display', 'none');
          $('#register-not-biuro').css('display', 'block');
          $('#register-dalej').css('display', 'block');
          $('#register-wyloguj').css('display', 'block');
        }
      }
    }
  });
});