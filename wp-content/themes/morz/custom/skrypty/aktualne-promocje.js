//P1 - promocje wyświetlane na stronach mag, b2b,b2c
//P2 - promocje wyświetlane na stronach promocja_fakir
//P3 - promocje wyświetlane na stronach kaper, gang
//P4 - promocje wyświetlane na stronie br online
//P5 - promocje wyświetlane na pozostałych stronach
// struktura rekordu ('P1','P2','P3','P4','P5', 'url tła', 'napis na czerwonym pasku','tytuł','opis','link','link tekst');


var szk = new Array();
var j = 0;

//AKTUALNE WERSJE
szk[j] = new Array('P1', 'P2', 'P3', 'P4', 'P5', 'https://wapro.pl/wp-content/uploads/2020/10/WAPRO-02.10-800x400-1.jpg', 'WARTO ZOBACZYĆ', 'WAPRO <span class="color-red">ERP</span> 2021 - GOTOWI NA JPK_V7', 'Sprawdź aktualne wersje programów WAPRO ERP oraz dostępność poszczególnych wariantów.', 'https://wapro.pl/aktualne-wersje-programow/', 'Aktualne wersje');
j++;

//Wybierz co chcesz
szk[j] = new Array('P1', 'P2', 'P3', 'P4', 'P5', 'https://wapro.pl/wp-content/uploads/2020/10/WAPRO-Best-800x400-1.jpg', 'PROMOCJA', '"Wybierz co chcesz" <br>wapro&nbsp;<span class="color-red">365</span> się opłaca!', 'WAPRO 365 to niska cena, wysoki <br>standard i bogaty pakiet dodatków!', 'https://wapro.pl/promocje/wybierz-co-chcesz/', 'Dowiedz się więcej');
j++;


//PAKIET ECOMMERCE
szk[j] = new Array('P1', '0', '0', '0', '0', 'https://wapro.pl/wp-content/uploads/2020/10/promocje-ecommerce.jpg', 'WARTO ZOBACZYĆ', 'Pakiet rozwiązań <span class="color-red">e-commerce</span>!', 'Wypróbuj komplet rozwiązań umożliwiający <br> szybką i profesjonalną sprzedaż w Internecie!', 'https://wapro.pl/erp/pakiet-e-commerce/', 'Dowiedz się więcej');
j++;

//WITAJ W WAPRO
szk[j] = new Array('P1', 'P2', 'P3', 'P4', 'P5', 'https://wapro.pl/wp-content/uploads/2020/10/promocje-witaj.jpg', 'PROMOCJA', 'Miło Cię widzieć w wapro&nbsp;<span class="color-red">365</span>!', 'Przygotowaliśmy dla Ciebie rabat oraz pakiet powitalny.', '/promocje/pakiet-powitalny-promocji-milo-cie-widziec-w-wapro/', 'Dowiedz się więcej');
j++;



//EHANDEL
szk[j] = new Array('P1', '0', '0', '0', '0', 'https://wapro.pl/wp-content/uploads/2020/10/promocje-ehandel.jpg', 'PROMOCJA', 'Każdy dzień jest <span class="color-red">e-handlowy</span>!', 'Powiększ abonament WAPRO B2C/B2B <br>aż do 720 dni. Już za złotówkę!', 'https://wapro.pl/promocje/e-handel/', 'Dowiedz się więcej');
j++;

//PPK
szk[j] = new Array('0', '0', 'P3', '0', '0', 'https://wapro.pl/wp-content/uploads/2020/10/promocje-ppk.jpg', 'WARTO ZOBACZYĆ', 'Poznaj wapro <span class="color-red">ppk 365</span>!', 'Samodzielny program do obsługi <br>i zarządzania PPK.', 'https://wapro.pl/erp/wapro-ppk-365/', 'Dowiedz się więcej');
j++;

//BIURO
szk[j] = new Array('0', 'P2', 'P3', 'P4', '0', 'https://wapro.pl/wp-content/uploads/2020/10/promocja-biuro30.jpg', 'PROMOCJA', '30% taniej - <span class="color-red">100 razy lepiej</span>!', 'Kup program w wariancie BIURO <br>i zyskaj setki nowych możliwości!', 'https://wapro.pl/promocje/wapro-biuro-30-taniej-100-razy-lepiej/', 'Dowiedz się więcej');
j++;


//RODO
szk[j] = new Array('P1', 'P2', 'P3', '0', '0', 'https://wapro.pl/wp-content/uploads/2020/10/promocja-rodo.jpg', 'WARTO ZOBACZYĆ', 'Program do obsługi <span class="color-red">RODO w firmie</span>', 'Wbudowane w system WAPRO ERP narzędzie ochrony danych osobowych.', 'https://wapro.pl/uslugi/wapro-rodo/', 'Dowiedz się więcej');
j++;

//korzystna rekomendacja
szk[j] = new Array('0', '0', '0', 'P4', '0', 'https://wapro.pl/wp-content/uploads/2019/12/minonline.jpg', 'WARTO ZOBACZYĆ', 'Klienci Biur Rachunkowych mają <span class="color-red">taniej</span>!', 'Poleć swojego klienta i zagwarantuj mu rabat na WAPRO Online', 'https://wapro.pl/promocje/korzystna-rekomendacja/', 'Dowiedz się więcej');
j++;