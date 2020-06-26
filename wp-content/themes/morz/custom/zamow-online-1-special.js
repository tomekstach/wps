jQuery(document).ready(function($) {
  $('.multistep-cf7-next').click(function(e) {
    if ($('.checkbox-serwis-biznes input').is(':checked') || $('.checkbox-serwis-prestiz input').is(':checked')) {
      $('#online-form-administrator').show();
      $('#online-form-administrator-bez').hide();
      $('#online-form-regulamin').attr('href', 'https://wapro.pl/doc/Regulamin_WAPRO_online_svr.pdf');
    } else {
      $('#online-form-administrator').hide();
      $('#online-form-administrator-bez').show();
      $('#online-form-regulamin').attr('href', 'https://wapro.pl/doc/Regulamin_WAPRO_online.pdf');
    }
  });

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

  $('.checkbox-serwis-biznes input:checkbox').bind('click', function() {
    $('.checkbox-serwis-prestiz input:checkbox').prop("checked", false);
    calculateSum();
  });

  $('.checkbox-serwis-prestiz input:checkbox').bind('click', function() {
    $('.checkbox-serwis-biznes input:checkbox').prop("checked", false);
    calculateSum();
  });

  $('.checkbox-backup-podst input:checkbox').bind('click', function() {
    $('.checkbox-backup-rozsz input:checkbox').prop("checked", false);
    calculateSum();
  });

  $('.checkbox-backup-rozsz input:checkbox').bind('click', function() {
    $('.checkbox-backup-podst input:checkbox').prop("checked", false);
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

    if ($('.checkbox-serwis-biznes input').is(':checked')) {
      sum = sum + 549;
    }

    if ($('.checkbox-serwis-prestiz input').is(':checked')) {
      sum = sum + 699;
    }

    if ($('.checkbox-backup-podst input').is(':checked')) {
      sum = sum + 49;
    }

    if ($('.checkbox-backup-rozsz input').is(':checked')) {
      sum = sum + 99;
    }

    $('#sum-netto').html(currencyFormatPL(sum));
    $('#abonament').val(sum);
    $('#sum-brutto').html(currencyFormatPL(sum * 1.23));

    if (($('#number-mag').val() > 0 || $('#number-mag-biznes').val() > 0 || $('#number-fakir').val() > 0 || $('#number-kaper').val() > 0) && $('#number-jpk-biznes').val() == 0 && $('#number-jpk').val() == 0) {
      $('.wpcf7-jpk-needed').show();
    } else {
      $('.wpcf7-jpk-needed').hide();
    }
  }
});