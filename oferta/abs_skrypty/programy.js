(function($) {

  function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
      results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
  }

  var program = getParameterByName('p');
  var cont_name = 'program-cont';

  var programy = {
    // Mag
    'mag': 'https://dealerzy.wapro.pl/erp/sprzedaz-magazyn',
    'mag-zakres': 'https://dealerzy.wapro.pl/erp/sprzedaz-magazyn/zakres-funkcjonalny/',
    'mag-historia': 'https://dealerzy.wapro.pl/erp/sprzedaz-magazyn/historia-zmian/',
    'mag-przyklady': 'https://dealerzy.wapro.pl/erp/sprzedaz-magazyn/przyklady-ekranow/',
    // Fakir
    'fakir': 'https://dealerzy.wapro.pl/erp/finanse-ksiegowosc/',
    'fakir-zakres': 'https://dealerzy.wapro.pl/erp/finanse-ksiegowosc/zakres-funkcjonalny/',
    'fakir-historia': 'https://dealerzy.wapro.pl/erp/finanse-ksiegowosc/historia-zmian/',
    'fakir-przyklady': 'https://dealerzy.wapro.pl/erp/finanse-ksiegowosc/przyklady-ekranow/',
    // KaPer
    'kaper': 'https://dealerzy.wapro.pl/erp/ksiega-podatkowa/',
    'kaper-zakres': 'https://dealerzy.wapro.pl/erp/ksiega-podatkowa/zakres-funkcjonalny/',
    'kaper-historia': 'https://dealerzy.wapro.pl/erp/ksiega-podatkowa/historia-zmian/',
    'kaper-przyklady': 'https://dealerzy.wapro.pl/erp/ksiega-podatkowa/przyklady-ekranow/',
    // Gang
    'gang': 'https://dealerzy.wapro.pl/erp/kadry-place/',
    'gang-zakres': 'https://dealerzy.wapro.pl/erp/kadry-place/zakres-funkcjonalny/',
    'gang-historia': 'https://dealerzy.wapro.pl/erp/kadry-place/historia-zmian/',
    'gang-przyklady': 'https://dealerzy.wapro.pl/erp/kadry-place/przyklady-ekranow/',
    // Fakturka
    'fakturka': 'https://dealerzy.wapro.pl/erp/fakturowanie/',
    'fakturka-zakres': 'https://dealerzy.wapro.pl/erp/fakturowanie/zakres-funkcjonalny/',
    'fakturka-historia': 'https://dealerzy.wapro.pl/erp/fakturowanie/historia-zmian/',
    'fakturka-przyklady': 'https://dealerzy.wapro.pl/erp/fakturowanie/przyklady-ekranow/',
    // Best
    'best': 'https://dealerzy.wapro.pl/erp/srodki-trwale/',
    'best-zakres': 'https://dealerzy.wapro.pl/erp/srodki-trwale/zakres-funkcjonalny/',
    'best-historia': 'https://dealerzy.wapro.pl/erp/srodki-trwale/historia-zmian/',
    'best-przyklady': 'https://dealerzy.wapro.pl/erp/srodki-trwale/przyklady-ekranow/',
    // B2B
    'b2b': 'https://dealerzy.wapro.pl/erp/b2b/',
    'b2b-zakres': 'https://dealerzy.wapro.pl/erp/b2b/zakres-funkcjonalny/',
    'b2b-funkcje': 'https://dealerzy.wapro.pl/erp/b2b/funkcje/',
    'b2b-przewagi': 'https://dealerzy.wapro.pl/erp/b2b/przewagi/',
    'b2b-uslugi': 'https://dealerzy.wapro.pl/erp/b2b/uslugi/',
    'b2b-historia': 'https://dealerzy.wapro.pl/erp/b2b/historia-zmian/',
    'b2b-przyklady': 'https://dealerzy.wapro.pl/erp/b2b/przyklady-wdrozen/',
    // B2C
    'b2c': 'https://dealerzy.wapro.pl/erp/b2c/',
    'b2c-zakres': 'https://dealerzy.wapro.pl/erp/b2c/zakres-funkcjonalny/',
    'b2c-funkcje': 'https://dealerzy.wapro.pl/erp/b2c/funkcje/',
    'b2c-przewagi': 'https://dealerzy.wapro.pl/erp/b2c/przewagi/',
    'b2c-uslugi': 'https://dealerzy.wapro.pl/erp/b2c/uslugi/',
    'b2c-historia': 'https://dealerzy.wapro.pl/erp/b2c/historia-zmian/',
    'b2c-przyklady': 'https://dealerzy.wapro.pl/erp/b2c/przyklady-wdrozen/',
    // JPK
    'jpk': 'https://dealerzy.wapro.pl/erp/wapro-jpk/',
    'jpk-zakres': 'https://dealerzy.wapro.pl/erp/wapro-jpk/zakres-funkcjonalny/',
    'jpk-historia': 'https://dealerzy.wapro.pl/erp/wapro-jpk/historia-zmian/',
    'jpk-przyklady': 'https://dealerzy.wapro.pl/erp/wapro-jpk/przyklady-ekranow/',
    // Online
    'online': 'https://dealerzy.wapro.pl/erp/wapro-online/',
    'online-mozliwosci': 'https://dealerzy.wapro.pl/erp/wapro-online/mozliwosci/',
    'online-kalkulacja': 'https://dealerzy.wapro.pl/erp/wapro-online/przykladowa-kalkulacja/',
    // Mobile
    'mobile': 'https://dealerzy.wapro.pl/erp/mobilna-firma/',
    'mobile-zakres': 'https://dealerzy.wapro.pl/erp/mobilna-firma/zakres-funkcjonalny/',
    'mobile-historia': 'https://dealerzy.wapro.pl/erp/mobilna-firma/historia-zmian/',
    'mobile-przyklady': 'https://dealerzy.wapro.pl/erp/mobilna-firma/przyklady-ekranow/',
    // Aukcje
    'aukcje': 'https://dealerzy.wapro.pl/erp/aukcje/',
    'aukcje-zakres': 'https://dealerzy.wapro.pl/erp/aukcje/zakres-funkcjonalny/',
    'aukcje-historia': 'https://dealerzy.wapro.pl/erp/aukcje/historia-zmian/',
    'aukcje-przyklady': 'https://dealerzy.wapro.pl/erp/aukcje/przyklady-ekranow/',
    // Analizy
    'analizy': 'https://dealerzy.wapro.pl/erp/analizy-wielowymiarowe/',
    'analizy-zakres': 'https://dealerzy.wapro.pl/erp/analizy-wielowymiarowe/zakres-funkcjonalny/',
    'analizy-historia': 'https://dealerzy.wapro.pl/erp/analizy-wielowymiarowe/historia-zmian/',
    'analizy-przyklady': 'https://dealerzy.wapro.pl/erp/analizy-wielowymiarowe/przyklady-ekranow/',
    // iBusiness
    'ibusiness': 'https://dealerzy.wapro.pl/erp/ibusiness/',
    'ibusiness-zakres': 'https://dealerzy.wapro.pl/erp/ibusiness/zakres-funkcjonalny/',
    'ibusiness-konfiguracja': 'https://dealerzy.wapro.pl/erp/ibusiness/jak-skonfigurowac/',
    // PPK
    'ppk': 'https://dealerzy.wapro.pl/erp/wapro-ppk-365/',
    'ppk-zakres': 'https://dealerzy.wapro.pl/erp/wapro-ppk-365/zakres-funkcjonalny/',
    'ppk-historia': 'https://dealerzy.wapro.pl/erp/wapro-ppk-365/historia-zmian/',
    'ppk-przyklady': 'https://dealerzy.wapro.pl/erp/wapro-ppk-365/przyklady-ekranow/',
    // Mobilny magazynier
    'mobile-mag': 'https://dealerzy.wapro.pl/erp/mobilny-magazynier/',
    'mobile-mag-zakres': 'https://dealerzy.wapro.pl/erp/mobilny-magazynier/zakres-funkcjonalny/',
    'mobile-mag-historia': 'https://dealerzy.wapro.pl/erp/mobilny-magazynier/historia-zmian/',
    'mobile-mag-przyklady': 'https://dealerzy.wapro.pl/erp/mobilny-magazynier/przyklady-ekranow/',
    // Mobilny inwentaryzator
    'mobile-st': 'https://dealerzy.wapro.pl/erp/mobilny-inwentaryzator/',
    'mobile-st-zakres': 'https://dealerzy.wapro.pl/erp/mobilny-inwentaryzator/zakres-funkcjonalny/',
    'mobile-st-historia': 'https://dealerzy.wapro.pl/erp/mobilny-inwentaryzator/historia-zmian/',
    'mobile-st-przyklady': 'https://dealerzy.wapro.pl/erp/mobilny-inwentaryzator/przyklady-ekranow/',
    // Warianty
    'porownanie-wariantow': 'https://dealerzy.wapro.pl/porownanie-wariantow/'
  };

  $('#' + cont_name).attr('src', "/get-from-wapro.php?src=" + programy[program]);
})(jQuery);