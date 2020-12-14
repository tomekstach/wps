jQuery(document).ready(function($) {
  $.ajax({
    type: "POST",
    url: "/wp-json/wl/v1/getLocations",
    beforeSend: function(xhr) {
      xhr.setRequestHeader('X-WP-Nonce', UmowaSerwisowaSettings.nonce);
    },
    success: function(data) {

      var arrayLength = data.length;
      var html = "";
      var buttonText = 'Wejdź';

      if (arrayLength > 0) {
        for (var i = 0; i < arrayLength; i++) {
          //console.log(data[i]);
          html += '<tr class="pp-table-row odd">';
          html += '<td><h6>' + data[i].post_title + '</h6></td>';
          html += '<td><h6>' + data[i].adres + '</h6><p>' + data[i].kod_pocztowy + ' ' + data[i].miejscowosc + '<br/>' + data[i].wojewodztwo + '</p></td>';
          html += '<td>' + data[i].biuro_online + '</td>';
          html += '<td><div class="vamtam-button-wrap vamtam-button-width-full"><a class="vamtam-button accent1 hover-accent6 button-solid icon-animation-disable" style="font-size: 17px; padding: 15px 40px;" role="button" href="/dla-biur/lokalizacja-biura/?loc=' + data[i].ID + '" target="_self"><span class="vamtam-button-text">' + buttonText + '</span></a></div></td>';
          html += "</tr>";
        }
      }

      $("#lista-lokalizacji tbody").html(html);
    }
  });
});