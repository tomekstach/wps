jQuery(document).ready(function($) {
  $.ajax({
    type: "POST",
    url: "/wp-json/wl/v1/getContracts",
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
    },
    success: function(data) {

      var arrayLength = data.length;
      var html = "";
      var buttonText = 'Dokończ';

      if (arrayLength > 0) {
        for (var i = 0; i < arrayLength; i++) {
          console.log(data[i]);
          if (data[i].files.length > 0) {
            buttonText = 'Prześlij kolejny plik';
          } else {
            buttonText = 'Dokończ';
          }
          html += '<tr class="pp-table-row odd">';
          html += '<td><div style="text-align: left;">nr ' + data[i].id + '</div></td>';
          html += '<td><h6>NIP: ' + data[i].NIP + '</h6><p>' + data[i].nazwa_firmy_klienta + '</p></td>';
          html += '<td><h6>' + data[i].first_name + ' ' + data[i].last_name + '</h6><p>' + data[i].tel_klienta + '<br/>' + data[i].e_mail_klienta + '</p></td>';
          html += '<td>' + data[i].data_zgloszenia + '</td>';
          html += '<td><div class="vamtam-button-wrap vamtam-button-width-full"><a class="vamtam-button accent1 hover-accent6 button-solid icon-animation-disable" style="font-size: 17px; padding: 15px 40px;" role="button" href="/umowa-serwisowa-krok-4/?contract=' + data[i].id + '" target="_self"><span class="vamtam-button-text">' + buttonText + '</span></a></div></td>';
          html += "</tr>";
        }
      }

      $("#lista-umow tbody").html(html);
    }
  });
});