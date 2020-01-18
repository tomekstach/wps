jQuery(document).ready(function($) {
  var contract = getUrlVars()["contract"];

  var _href = $("#next-krok-4 a.vamtam-button").attr("href");
  $("#next-krok-4 a.vamtam-button").attr("href", _href + "?contract=" + contract);

  $("input[name='redirect_to']").val(window.location.href);
  console.log('URL: ' + window.location.href);
});

function getUrlVars() {
  var vars = {};
  var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
    function(m, key, value) {
      vars[key] = value;
    });
  return vars;
}