jQuery(document).ready(function($) {

  var emptyCell = '<div class="fl-col fl-col-small"><div class="fl-col-content fl-node-content vamtam-show-bg-image"></div></div>';

  /* AUTORYZACJE */
  if (autoryzacjeText.trim() != '') {
    var autoryzacje = autoryzacjeText.split('|');
    var autoryzacjeHTML = '';
    var autoryzacjeData = [];

    for (var i = 0; i < autoryzacje.length; i++) {
      autoryzacjeData = autoryzacje[i].split(';');
      if (i % 4 == 0) {
        if (i > 0) {
          autoryzacjeHTML += '</div>';
        }
        autoryzacjeHTML += '<div class="fl-col-group fl-col-group-equal-height fl-col-group-align-top fl-col-group-custom-width badges">';
      }
      autoryzacjeHTML += '<div class="fl-col fl-col-small fl-col-has-cols ' + autoryzacjeData[2] + '"><div class="fl-col-content fl-node-content vamtam-show-bg-image badge"><div class="fl-module fl-module-rich-text badge-year"><div class="fl-module-content fl-node-content"><div class="fl-rich-text"><p>' + autoryzacjeData[0] + '</p></div></div></div><div class="fl-col-group fl-col-group-nested fl-col-group-equal-height fl-col-group-align-top"><div class="fl-col fl-col-small badge-icon"><div class="fl-col-content fl-node-content vamtam-show-bg-image"><div class="fl-module fl-module-vamtam-icon"><div class="fl-module-content fl-node-content"> <span class="fl-icon-wrap"> <span class="fl-icon"><i class="gsui-icon gsui-icon--rewards-medal-5 "></i> </span> </span></div></div></div></div><div class="fl-col badge-text"><div class="fl-col-content fl-node-content vamtam-show-bg-image"><div class="fl-module fl-module-rich-text "><div class="fl-module-content fl-node-content"><div class="fl-rich-text"><p>' + autoryzacjeData[1] + '</p></div></div></div></div></div></div></div></div>';
    }
    while (i % 4 > 0) {
      autoryzacjeHTML += emptyCell;
      i++;
    }
    autoryzacjeHTML += '</div>';

    $('#badges-container').html(autoryzacjeHTML);
  } else {
    $('#certyfikaty-header').css('display', 'none');
  }

  /* SZKOLENIA */
  if (szkoleniaText.trim() != '') {
    var szkolenia = szkoleniaText.split('|');
    var szkoleniaHTML = '';
    var szkoleniaData = [];

    for (var i = 0; i < szkolenia.length; i++) {
      szkoleniaData = szkolenia[i].split(';');
      if (i % 4 == 0) {
        if (i > 0) {
          szkoleniaHTML += '</div>';
        }
        szkoleniaHTML += '<div class="fl-col-group fl-col-group-equal-height fl-col-group-align-top fl-col-group-custom-width badges">';
      }
      szkoleniaHTML += '<div class="fl-col fl-col-small fl-col-has-cols ' + szkoleniaData[2] + '"><div class="fl-col-content fl-node-content vamtam-show-bg-image badge"><div class="fl-module fl-module-rich-text badge-year"><div class="fl-module-content fl-node-content"><div class="fl-rich-text"><p>' + szkoleniaData[0] + '</p></div></div></div><div class="fl-col-group fl-col-group-nested fl-col-group-equal-height fl-col-group-align-top"><div class="fl-col fl-col-small badge-icon"><div class="fl-col-content fl-node-content vamtam-show-bg-image"><div class="fl-module fl-module-vamtam-icon"><div class="fl-module-content fl-node-content"> <span class="fl-icon-wrap"> <span class="fl-icon"><i class="psui-icon psui-icon--graduation-hat "></i> </span> </span></div></div></div></div><div class="fl-col badge-text"><div class="fl-col-content fl-node-content vamtam-show-bg-image"><div class="fl-module fl-module-rich-text "><div class="fl-module-content fl-node-content"><div class="fl-rich-text"><p>' + szkoleniaData[1] + '</p></div></div></div></div></div></div></div></div>';
    }
    while (i % 4 > 0) {
      szkoleniaHTML += emptyCell;
      i++;
    }
    szkoleniaHTML += '</div>';

    $('#badges-container-szkolenia').html(szkoleniaHTML);
  } else {
    $('#szkolenia-header').css('display', 'none');
  }

  if (autoryzacjeText.trim() == '' && szkoleniaText.trim() == '') {
    $('.badges-container').css('display', 'none');
  }
});
