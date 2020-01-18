jQuery(document).ready(function($) {
  var d = new Date();
  var month = d.getMonth() + 1;
  var day = d.getDate();
  var output = (day < 10 ? '0' : '') + day + '/' +
    (month < 10 ? '0' : '') + month + '/' +
    d.getFullYear();

  $('#data-umowy').val(output);
  $('#data-podpisania-umowy').html(output);
});