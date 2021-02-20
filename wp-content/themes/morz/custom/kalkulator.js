var products = {
  "WAPRO Mag": {
    "Start": [350],
    "Biznes": [1090, 1090],
    "Prestiż": [2090, 2090],
    "Prestiż PLUS": [2390, 2390]
  },
  "WAPRO Fakir": {
    "Biznes": [2090, 1990],
    "Prestiż": [3290, 3190],
    "Biuro": [2990, 2190],
    "Biuro Plus": [3390, 3190]
  },
  "WAPRO Kaper": {
    "Start": [350],
    "Biznes": [1090, 1090],
    "Prestiż": [1290, 1290],
    "Biuro": [1690, 1690]
  }
};

jQuery(document).ready(function($) {
  $('.kalk_wariant_p option:selected').prop("selected", false);
  $('.kalk_wariant_s option:selected').prop("selected", false);
  $('.kalk_wariant_p_os option:selected').prop("selected", false);
  $('.kalk_wariant_s_os option:selected').prop("selected", false);
  $('.kalk_wariant_portale_hr option:selected').prop("selected", false);
  $('.kalk_ps option:selected').prop("selected", false);
  $('.kalk_wariant_p').attr('disabled', true);
  $('.kalk_wariant_s').attr('disabled', true);
  $('.kalk_wariant_p_os').attr('disabled', true);
  $('.kalk_wariant_s_os').attr('disabled', true);
  $('.kalk_wariant_portale_hr').attr('disabled', true);
  $('.kalk_ps').attr('disabled', true);
  $('.kalk_number').attr('disabled', true);
  $('.kalk-opis .wpcf7-list-item-label').html('');

  $('.kalk-row').each(function(index) {
    var produkt = this;
    $(produkt).find('.cf7-row.kalk-dane').hide();

    $(produkt).find('.kalk_produkt input[type=checkbox]').click(function(e) {
      if ($(this).prop('checked')) {
        $(produkt).find('.cf7-row.kalk-dane').show();
        $(produkt).find('.kalk_wariant_p').attr('disabled', false);
        $(produkt).find('.kalk_wariant_s').attr('disabled', false);
        $(produkt).find('.kalk_wariant_p_os').attr('disabled', false);
        $(produkt).find('.kalk_wariant_s_os').attr('disabled', false);
        $(produkt).find('.kalk_wariant_portale_hr').attr('disabled', false);
        $(produkt).find('.kalk_ps').attr('disabled', false);
        $(produkt).find('.kalk_number').attr('disabled', false);
      } else {
        $(produkt).find('.cf7-row.kalk-dane').hide();
        $(produkt).find('.kalk_wariant_p option:selected').prop("selected", false);
        $(produkt).find('.kalk_wariant_p').attr('disabled', true);
        $(produkt).find('.kalk_wariant_s option:selected').prop("selected", false);
        $(produkt).find('.kalk_wariant_s').attr('disabled', true);
        $(produkt).find('.kalk_wariant_p_os option:selected').prop("selected", false);
        $(produkt).find('.kalk_wariant_p_os').attr('disabled', true);
        $(produkt).find('.kalk_wariant_s_os option:selected').prop("selected", false);
        $(produkt).find('.kalk_wariant_s_os').attr('disabled', true);
        $(produkt).find('.kalk_wariant_portale_hr option:selected').prop("selected", false);
        $(produkt).find('.kalk_wariant_portale_hr').attr('disabled', true);
        $(produkt).find('.kalk_portale_hr option:selected').prop("selected", false);
        $(produkt).find('.kalk_ps').attr('disabled', true);
        $(produkt).find('.kalk_number').val("");
        $(produkt).find('.kalk_number').attr('disabled', true);
      }

      updatePodsuma();
    });

    $(produkt).find('.kalk_ps').on('change', function() {
      if ($(produkt).find('.kalk_ps option:selected').text() == '8.5x.x -') {
        $(produkt).find('.kalk_wariant_p option:selected').prop("selected", false);
        $(produkt).find('.kalk_wariant_p_os option:selected').prop("selected", false);
      } else {
        $(produkt).find('.kalk_wariant_s option:selected').prop("selected", false);
        $(produkt).find('.kalk_wariant_s_os option:selected').prop("selected", false);
      }
    });

    $(produkt).find('.kalk_portale_hr').on('change', function() {
      if ($(produkt).find('.kalk_portale_hr option:selected').text() == 'miesieczny') {
        $(produkt).find('.kalk_wariant_portale_hr option:selected').prop("selected", false);
        $(produkt).find('.kalk_number').val("");
      } else {
        $(produkt).find('.kalk_wariant_portale_hr option:selected').prop("selected", false);
        $(produkt).find('.kalk_number').val("");
      }
      updatePodsuma();
    });

    $(produkt).find('.kalk_wariant_p').on('change', function() {
      $(produkt).find('.kalk_wariant_p_os option:selected').prop("selected", false);
    });

    $(produkt).find('.kalk_wariant_s').on('change', function() {
      $(produkt).find('.kalk_wariant_s_os option:selected').prop("selected", false);
    });

    $(produkt).find('.kalk_wariant_p').on('change', function() {
      updatePodsuma();
    });

    $(produkt).find('.kalk_wariant_s').on('change', function() {
      updatePodsuma();
    });

    $(produkt).find('.kalk_wariant_p_os').on('change', function() {
      updatePodsuma();
    });

    $(produkt).find('.kalk_wariant_s_os').on('change', function() {
      updatePodsuma();
    });

    $(produkt).find('.kalk_wariant_portale_hr').on('change', function() {
      updatePodsuma();
    });

    $(produkt).find('.kalk_number').on('input', function(e) {
      updatePodsuma();
    });

    $(produkt).find('.kalk_ps').on('change', function() {
      updatePodsuma();
    });
  });

  function updatePodsuma() {
    var html = '<table class="pp-table-5cba11d3badf1 pp-table-content tablesaw"><thead><tr><th id="pp-table-col-1" clas="pp-table-col">Szczegóły</th><th id="pp-table-col-2" clas="pp-table-col">Ilość stanowisk</th><th id="pp-table-col-3" clas="pp-table-col">Cena netto za szt.</th><th id="pp-table-col-4" clas="pp-table-col">Kwota netto</th></tr></thead><tbody>'
    var htmlStandard = '<table class="pp-table-5cba11d3badf1 pp-table-content tablesaw"><thead><tr><th id="pp-table-col-1" clas="pp-table-col">Szczegóły</th><th id="pp-table-col-2" clas="pp-table-col">Ilość stanowisk</th><th id="pp-table-col-3" clas="pp-table-col">Cena netto za szt.</th><th id="pp-table-col-4" clas="pp-table-col">Kwota netto</th></tr></thead><tbody>'
    var specjalna = false;
    var demo = true;
    var iloscProduktow = 0;
    var suma = 0
    var suma_standard = 0;
    var gratis = false;

    $('.kalk-row').each(function(index) {
      var produkt = this;
      var wersja = '';
      var cena = 0;
      var cena_standard = [];
      var ilosc = 0;
      var produktNazwa = '';

      if ($(produkt).find('.kalk_wariant_p option:selected').text() == 'Start' || $(produkt).find('.kalk_wariant_s option:selected').text() == 'Start') {
        $(produkt).find('.kalk_number').attr('max', '1');
        $(produkt).find('.kalk_number').val('1');
      } else {
        $(produkt).find('.kalk_number').attr('max', '100000000');
      }

      if ($(produkt).find('.kalk_produkt input[type=checkbox]').prop('checked')) {

        produktNazwa = $(produkt).find('.kalk_produkt input[type=checkbox]').val();

        if (produktNazwa != 'WAPRO Gang' && produktNazwa != 'WAPRO Best' && produktNazwa != 'Portale HR') {
          if ($(produkt).find('.kalk_wariant_p option:selected').text() != '-- Wybierz wariant --') {
            wersja = $(produkt).find('.kalk_wariant_p option:selected').text();
            cena = parseInt($(produkt).find('.kalk_wariant_p option:selected').val());
            specjalna = $(produkt).find('.kalk_ps option:selected').val();

            if (parseInt(discount[specjalna]) > 0) {
              after_discount = (products[produktNazwa][wersja][0] * (100 - discount[specjalna])) / 100;
            } else {
              after_discount = products[produktNazwa][wersja][0];
            }

            if (after_discount > 175) {
              cena_standard[0] = after_discount;
            } else {
              cena_standard[0] = 175;
            }

            if (products[produktNazwa][wersja][1]) {
              if (parseInt(discount[specjalna]) > 0) {
                cena_standard[1] = (products[produktNazwa][wersja][1] * (100 - discount[specjalna])) / 100;
              } else {
                cena_standard[1] = products[produktNazwa][wersja][1];
              }
            }
          } else if ($(produkt).find('.kalk_wariant_s option:selected').text() != '-- Wybierz wariant --') {
            wersja = $(produkt).find('.kalk_wariant_s option:selected').text();
            cena = parseInt($(produkt).find('.kalk_wariant_s option:selected').val());
            specjalna = $(produkt).find('.kalk_ps option:selected').val();

            if (parseInt(discount[specjalna]) > 0) {
              after_discount = (products[produktNazwa][wersja][0] * (100 - discount[specjalna])) / 100;
            } else {
              after_discount = products[produktNazwa][wersja][0];
            }

            if (after_discount > 175) {
              cena_standard[0] = after_discount;
            } else {
              cena_standard[0] = 175;
            }

            if (products[produktNazwa][wersja][1]) {
              if (parseInt(discount[specjalna]) > 0) {
                cena_standard[1] = ((products[produktNazwa][wersja][1] * (100 - discount[specjalna])) / 100);
              } else {
                cena_standard[1] = products[produktNazwa][wersja][1];
              }
            }
          }
        } else {
          if (produktNazwa == 'Portale HR' && $(produkt).find('.kalk_portale_hr option:selected').text() != '-- Wybierz abonament --') {
            wersja = $(produkt).find('.kalk_wariant_portale_hr option:selected').not("[value=0]").text();
            wersja = $(produkt).find('.kalk_portale_hr option:selected').text() + ' ' + wersja;
            cena = parseInt($(produkt).find('.kalk_wariant_portale_hr option:selected').not("[value=0]").val());

            if ($(produkt).find('.kalk_wariant_portale_hr option:selected').not("[value=0]").val() != '0' && !(cena > 0)) {
              cena = $(produkt).find('.kalk_wariant_portale_hr option:selected').not("[value=0]").val();
            }
          } else if ($(produkt).find('.kalk_wariant_p option:selected').text() != '-- Wybierz wariant --') {
            wersja = $(produkt).find('.kalk_wariant_p_os option:selected').not("[value=0]").text();
            wersja = $(produkt).find('.kalk_wariant_p option:selected').text() + ' ' + wersja;
            cena = parseInt($(produkt).find('.kalk_wariant_p_os option:selected').not("[value=0]").val());
          } else if ($(produkt).find('.kalk_wariant_s option:selected').text() != '-- Wybierz wariant --') {
            wersja = $(produkt).find('.kalk_wariant_s_os option:selected').not("[value=0]").text();
            wersja = $(produkt).find('.kalk_wariant_s option:selected').text() + ' ' + wersja;
            cena = parseInt($(produkt).find('.kalk_wariant_s_os option:selected').not("[value=0]").val());
          }
        }

        if ($(produkt).find('.kalk_ps option:selected').text() == '8.5x.x -') {
          var cenaRodzaj = 'cena specjalna *';
        } else {
          var cenaRodzaj = 'cena podstawowa';
        }

        if ($(produkt).find('.kalk_ps option:selected').text() == '8.5x.x -' || $(produkt).find('.kalk_ps option:selected').text() == '8.4x.x' || $(produkt).find('.kalk_ps option:selected').text() == '8.3x.x lub starszą') {
          demo = false;
        }

        if ($(produkt).find('.kalk_number').val()) {
          ilosc = parseInt($(produkt).find('.kalk_number').val());
          if (produktNazwa != 'Portale HR') {
            iloscProduktow += ilosc;
          }

          if (produktNazwa == 'Portale HR' && ilosc > 0) {
            gratis = true;
          }
        }

        if (ilosc > 0 && cena > 0 && typeof(cena) == 'number') {
          html += '<tr class="pp-table-row odd"><td>' + produktNazwa + ' 365 ' + wersja + '</td><td>' + ilosc + '</td><td>' + cenaRodzaj + ' ' + cena.toFixed(2).toString().replace(".", ",") + ' PLN</td><td class="kwota"><span>' + (cena * ilosc).toFixed(2).toString().replace(".", ",") + '</span> PLN</td></tr>';
          if (wersja != 'Start' && wersja != 'START' && specjalna && specjalna != 'Nie mam (nowy zakup)') {
            html += '<tr class="pp-table-row odd"><td colspan="4">Dodatkowo GRATIS 1 stanowisko ' + produktNazwa + ' 365 ' + wersja + ' na pierwszy rok <strong>(promocja <a href="https://wapro.pl/promocje/wybierz-co-chcesz/" target="_blank" class="kalk-promo-link">Wybierz co chcesz</a>)</strong></td></tr>';
          }

          if (produktNazwa == 'WAPRO Mag' && (wersja == 'Prestiż' || wersja == 'Prestiż PLUS')) {
            htmlStandard += '<tr class="pp-table-row odd"><td colspan="4">Dodatkowo GRATIS WAPRO B2B na pierwszy rok <strong>(promocja <a href="https://wapro.pl/promocje/wybierz-co-chcesz/" target="_blank" class="kalk-promo-link">Wybierz co chcesz</a>)</strong></td></tr>';
          }

          suma += cena * ilosc;
          if (wersja != 'Start' && produktNazwa != 'WAPRO Gang' && produktNazwa != 'WAPRO Best' && produktNazwa != 'Portale HR') {
            htmlStandard += '<tr class="pp-table-row odd"><td>' + produktNazwa + ' ' + wersja + ' licencja na pierwsze stanowisko</td><td>1</td><td>' + cena_standard[0].toFixed(2).toString().replace(".", ",") + ' PLN</td><td class="kwota"><span>' + cena_standard[0].toFixed(2).toString().replace(".", ",") + '</span> PLN</td></tr>';
            suma_standard += cena_standard[0];
            //console.log('cena_standard[0]: ' + cena_standard[0]);
            //console.log('cena_standard[1]: ' + cena_standard[1]);
            if (ilosc > 1) {
              suma_standard += (ilosc - 1) * cena_standard[1];
              htmlStandard += '<tr class="pp-table-row odd"><td>' + produktNazwa + ' ' + wersja + ' licencja na dodatkowe stanowiska</td><td>' + (ilosc - 1) + '</td><td>' + cena_standard[1].toFixed(2).toString().replace(".", ",") + ' PLN</td><td class="kwota"><span>' + (cena_standard[1] * (ilosc - 1)).toFixed(2).toString().replace(".", ",") + '</span> PLN</td></tr>';
            }
          } else if (produktNazwa != 'WAPRO Gang' && produktNazwa != 'WAPRO Best' && produktNazwa != 'Portale HR') {
            htmlStandard += '<tr class="pp-table-row odd"><td>' + produktNazwa + ' ' + wersja + '</td><td>1</td><td>' + cena_standard[0].toFixed(2).toString().replace(".", ",") + ' PLN</td><td class="kwota"><span>' + cena_standard[0].toFixed(2).toString().replace(".", ",") + '</span> PLN</td></tr>';
            //console.log('cena_standard[0]: ' + cena_standard[0]);
            suma_standard += cena_standard[0];
          }
        } else if (ilosc > 0 && cena != '' && typeof(cena) == 'string') {
          html += '<tr class="pp-table-row odd"><td>' + produktNazwa + ' 365 ' + wersja + '</td><td>' + ilosc + '</td><td>' + cena + '</td><td class="kwota"><span>' + cena + '</span></td></tr>';
        }

        if (produktNazwa == 'Portale HR') {
          html += '<tr class="pp-table-row odd"><td colspan="4">UWAGA: wymagany WAPRO Gang w najnowszej wersji!</td></tr>';
        }
      }
    });

    if (iloscProduktow > 0) {
      html += '<tr class="pp-table-row odd"><td colspan="4">Dodatkowo GRATIS WAPRO Analizy 365 na pierwszy rok <strong>(promocja <a href="https://wapro.pl/promocje/wybierz-co-chcesz/" target="_blank" class="kalk-promo-link">Wybierz co chcesz</a>)</strong></td></tr>';
    }

    html += '</tbody></table>';

    var zysk = suma_standard - suma;
    htmlStandard += '</tbody></table>';

    //if (zysk > 0 && !cennik_ogolny) {
    if (!cennik_ogolny) {
      $('#calculated-standard').val(suma_standard.toFixed(2).toString().replace(".", ",") + ' PLN netto');

      if (suma_standard > 0) {
        $('#kalk-podsuma-standard').html(htmlStandard);
      } else {
        $('#kalk-podsuma-standard').html('');
      }

      html += '<div id="podsuma-zysk">';

      if (zysk > 0) {
        html += '<h4>Na abonamencie oszczędzasz</h4><p>';
        html += '<span>' + ((zysk * 100) / suma_standard).toFixed(0).toString().replace(".", ",") + '</span> % czyli <span>' + zysk.toFixed(2).toString().replace(".", ",") + '</span> PLN</p>';
      }

      if (demo) {
        html += '<a href="https://wapro.pl/pobierz-demo/" target="_blank">Pobierz demo -></a></div>'
      } else {
        html += '<a href="https://wapro24.assecobs.pl/asystent/Default.htm" target="_blank">Pobierz aktualizacje -></a></div>'
      }
    } else {
      //console.log('Zysk: ' + suma_standard + ' - ' + suma);
      html += '<div id="podsuma-zysk">';

      if (demo) {
        html += '<a href="https://wapro.pl/pobierz-demo/" target="_blank">Pobierz demo -></a></div>'
      } else {
        html += '<a href="https://wapro24.assecobs.pl/asystent/Default.htm" target="_blank">Pobierz aktualizacje -></a></div>'
      }

      $('#kalk-podsuma-standard').html('');
      $('#calculated-standard').val('0,00 PLN');
    }

    if (suma > 0 || gratis == true) {
      $('.kalk-suma').each(function(index) {
        if ($(this).css('display') == 'none') {
          $(this).css('display', 'block');
        }
      });
      $('#kalk-podsuma').html(html);
    } else {
      $('#kalk-podsuma').html('');
    }
  }
});