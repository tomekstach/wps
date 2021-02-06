jQuery(document).ready(function($) {

    $('.program select').change(function() {
        $('#cf7-container-builder-3502589 select').val("wybierz numer wersji");
        $('#cf7-container-builder-3502589 option').removeAttr('selected');
    });
});