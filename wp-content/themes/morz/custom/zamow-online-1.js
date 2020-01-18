jQuery(document).ready(function($) {
    $('.program-number').bind('keyup mouseup', function() {
        calculateSum();
    });

    $('.program-number-biznes').bind('keyup mouseup', function() {
        calculateSum();
    });

    $('.program-number-special').bind('keyup mouseup', function() {
        calculateSum();
    });

    $('.jpk-free input:checkbox').bind('click', function() {
        calculateSum();
    });

    calculateSum();

    function currencyFormatPL(num) {
        return (
                num
                .toFixed(2) // always two decimal digits
                .replace('.', ',') // replace decimal point character with ,
                .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ') + ' zł'
            ) // use . as a separator
    }

    function calculateSum() {
        var sum = 0;
        var sum1 = 0;
        var sum2 = 0;
        $('.program-number').each(function() {
            if (parseInt($(this).val()) > 0) sum1 = sum1 + parseInt($(this).val());
        });
        $('.program-number-biznes').each(function() {
            if (parseInt($(this).val()) > 0) sum2 = sum2 + parseInt($(this).val());
        });

        sum = (sum1 * 159) + (sum2 * 129);

        $('#sum-netto').html(currencyFormatPL(sum));
        $('#abonament').val(sum);
        $('#sum-brutto').html(currencyFormatPL(sum * 1.23));
    }
});