function promocja_mag_b2b_b2c() {
  var JP1 = 0;
  var koniecc = szk.length;
  for (i = 0; i < koniecc; i++) {
    if (szk[i][0] == 'P1') {
      JP1 = 1;
    }
  }
  if (JP1 > 0) {
    document.write('<div class="swiper-container">');
    document.write('<div class="swiper-wrapper">');
    for (i = 0; i < koniecc; i++) {
      if (szk[i][0] == 'P1') {
        document.write('<div class="swiper-slide" style="background-image: url(\'' + szk[i][5] + '\');">');
        document.write(' <div class="swiper-content"><table style="width: 100%;" cellspacing="0" cellpadding="0">');
        document.write('<tbody><tr style="border:0px;"><td style="padding: 15px 30px 0 30px; background-color:#d42027; "><p style="color:#ffffff;">' + szk[i][6] + '</p></td><td></td></tr>');
        document.write('<tr style="border:0px;"><td class="promocja-text" style="background-color:#ffffff; padding: 20px 30px 30px 30px;">');
        document.write('<h4 style="text-align: left; font-size: 26px;">' + szk[i][7] + '</h4>');
        document.write('<p style="text-align: left; font-size: 14px;">' + szk[i][8] + '</p>');
        document.write('<p style="text-align: left; padding-top: 10px; font-size: 14px;"><a href="' + szk[i][9] + '">');
        document.write('<span class="promocja-button">' + szk[i][10] + '</span> </a></p></td>');
        document.write('<td class="promocja-image" style="padding: 0; margin: 0;"><div></div></td>');
        document.write('</tr></tbody></table></div></div>');
      }
    }
    document.write('</div>');
    document.write('<div class="swiper-pagination"></div>');
    document.write('</div>');
  }
}

function promocja_fakir() {
  var JP2 = 0;
  var koniecc = szk.length;
  for (i = 0; i < koniecc; i++) {
    if (szk[i][1] == 'P2') {
      JP2 = 1;
    }
  }
  if (JP2 > 0) {
    document.write('<div class="swiper-container">');
    document.write('<div class="swiper-wrapper">');
    for (i = 0; i < koniecc; i++) {
      if (szk[i][1] == 'P2') {
        document.write('<div class="swiper-slide" style="background-image: url(\'' + szk[i][5] + '\');">');
        document.write(' <div class="swiper-content"><table style="width: 100%;" cellspacing="0" cellpadding="0">');
        document.write('<tbody><tr style="border:0px;"><td style="padding: 15px 30px 0 30px; background-color:#d42027; "><p style="color:#ffffff;">' + szk[i][6] + '</p></td><td></td></tr>');
        document.write('<tr style="border:0px;"><td class="promocja-text" style="background-color:#ffffff; padding: 20px 30px 30px 30px;">');
        document.write('<h4 style="text-align: left; font-size: 26px;">' + szk[i][7] + '</h4>');
        document.write('<p style="text-align: left; font-size: 14px;">' + szk[i][8] + '</p>');
        document.write('<p style="text-align: left; padding-top: 10px; font-size: 14px;"><a href="' + szk[i][9] + '">');
        document.write('<span class="promocja-button">' + szk[i][10] + '</span> </a></p></td>');
        document.write('<td class="promocja-image" style="padding: 0; margin: 0;"><div></div></td>');
        document.write('</tr></tbody></table></div></div>');
      }
    }
    document.write('</div>');
    document.write('<div class="swiper-pagination"></div>');
    document.write('</div>');
  }
}


function promocja_kaper_gang() {
  var JP3 = 0;
  var koniecc = szk.length;
  for (i = 0; i < koniecc; i++) {
    if (szk[i][2] == 'P3') {
      JP3 = 1;
    }
  }
  if (JP3 > 0) {
    document.write('<div class="swiper-container">');
    document.write('<div class="swiper-wrapper">');
    for (i = 0; i < koniecc; i++) {
      if (szk[i][2] == 'P3') {
        document.write('<div class="swiper-slide" style="background-image: url(\'' + szk[i][5] + '\');">');
        document.write(' <div class="swiper-content"><table style="width: 100%;" cellspacing="0" cellpadding="0">');
        document.write('<tbody><tr style="border:0px;"><td style="padding: 15px 30px 0 30px; background-color:#d42027; "><p style="color:#ffffff;">' + szk[i][6] + '</p></td><td></td></tr>');
        document.write('<tr style="border:0px;"><td class="promocja-text" style="background-color:#ffffff; padding: 20px 30px 30px 30px;">');
        document.write('<h4 style="text-align: left; font-size: 26px;">' + szk[i][7] + '</h4>');
        document.write('<p style="text-align: left; font-size: 14px;">' + szk[i][8] + '</p>');
        document.write('<p style="text-align: left; padding-top: 10px; font-size: 14px;"><a href="' + szk[i][9] + '">');
        document.write('<span class="promocja-button">' + szk[i][10] + '</span> </a></p></td>');
        document.write('<td class="promocja-image" style="padding: 0; margin: 0;"><div></div></td>');
        document.write('</tr></tbody></table></div></div>');
      }
    }
    document.write('</div>');
    document.write('<div class="swiper-pagination"></div>');
    document.write('</div>');
  }
}


function promocja_br_online() {
  var JP4 = 0;
  var koniecc = szk.length;
  for (i = 0; i < koniecc; i++) {
    if (szk[i][3] == 'P4') {
      JP4 = 1;
    }
  }
  if (JP4 > 0) {
    document.write('<div class="swiper-container">');
    document.write('<div class="swiper-wrapper">');
    for (i = 0; i < koniecc; i++) {
      if (szk[i][3] == 'P4') {
        document.write('<div class="swiper-slide" style="background-image: url(\'' + szk[i][5] + '\');">');
        document.write(' <div class="swiper-content"><table style="width: 100%;" cellspacing="0" cellpadding="0">');
        document.write('<tbody><tr style="border:0px;"><td style="padding: 15px 30px 0 30px; background-color:#d42027; "><p style="color:#ffffff;">' + szk[i][6] + '</p></td><td></td></tr>');
        document.write('<tr style="border:0px;"><td class="promocja-text" style="background-color:#ffffff; padding: 20px 30px 30px 30px;">');
        document.write('<h4 style="text-align: left; font-size: 26px;">' + szk[i][7] + '</h4>');
        document.write('<p style="text-align: left; font-size: 14px;">' + szk[i][8] + '</p>');
        document.write('<p style="text-align: left; padding-top: 10px; font-size: 14px;"><a href="' + szk[i][9] + '">');
        document.write('<span class="promocja-button">' + szk[i][10] + '</span> </a></p></td>');
        document.write('<td class="promocja-image" style="padding: 0; margin: 0;"><div></div></td>');
        document.write('</tr></tbody></table></div></div>');
      }
    }
    document.write('</div>');
    document.write('<div class="swiper-pagination"></div>');
    document.write('</div>');
  }
}


function promocja_pozostale() {
  var JP5 = 0;
  var koniecc = szk.length;
  for (i = 0; i < koniecc; i++) {
    if (szk[i][4] == 'P5') {
      JP5 = 1;
    }
  }
  if (JP5 > 0) {
    document.write('<div class="swiper-container">');
    document.write('<div class="swiper-wrapper">');
    for (i = 0; i < koniecc; i++) {
      if (szk[i][4] == 'P5') {
        document.write('<div class="swiper-slide" style="background-image: url(\'' + szk[i][5] + '\');">');
        document.write(' <div class="swiper-content"><table style="width: 100%;" cellspacing="0" cellpadding="0">');
        document.write('<tbody><tr style="border:0px;"><td style="padding: 15px 30px 0 30px; background-color:#d42027; "><p style="color:#ffffff;">' + szk[i][6] + '</p></td><td></td></tr>');
        document.write('<tr style="border:0px;"><td class="promocja-text" style="background-color:#ffffff; padding: 20px 30px 30px 30px;">');
        document.write('<h4 style="text-align: left; font-size: 26px;">' + szk[i][7] + '</h4>');
        document.write('<p style="text-align: left; font-size: 14px;">' + szk[i][8] + '</p>');
        document.write('<p style="text-align: left; padding-top: 10px; font-size: 14px;"><a href="' + szk[i][9] + '">');
        document.write('<span class="promocja-button">' + szk[i][10] + '</span> </a></p></td>');
        document.write('<td class="promocja-image" style="padding: 0; margin: 0;"><div></div></td>');
        document.write('</tr></tbody></table></div></div>');
      }
    }
    document.write('</div>');
    document.write('<div class="swiper-pagination"></div>');
    document.write('</div>');
  }
}