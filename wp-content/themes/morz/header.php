<?php
/**
 * Header template
 *
 * @package vamtam/morz
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">

<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="theme-color" content="<?php echo sanitize_hex_color( vamtam_get_option( 'accent-color', 1 ) ) ?>">

	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

  <?php if (strpos(get_home_url(), 'dealerzy.wapro.pl') > 0):?>
    <link rel="stylesheet" id="custom-icons-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-icons.css?ver=2.3.2.6" type="text/css" media="all" />
    <script src="https://kit.fontawesome.com/7987af0305.js" crossorigin="anonymous"></script>
  <?php endif;?>

	<?php wp_head(); ?>

  <?php if (strpos(get_home_url(), 'dealerzy.wapro.pl') > 0):?>
    <link rel="stylesheet" id="custom-icons13-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-icons13.css?ver=2.3.2.6" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-additional.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-applications.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-business.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-computer.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-content.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-data-transform.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-education.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-email.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-files.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-ikony-czerwony-pasek.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-location-places.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-messages.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-phones-mobile-smart.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-rewards.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-server.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-shopping.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-transport.css?ver=2.3.2.7" type="text/css" media="all" />
    <link rel="stylesheet" id="custom-additional-css"  href="https://dealerzy.wapro.pl/wp-content/themes/morz/custom/icons/custom-users.css?ver=2.3.2.7" type="text/css" media="all" />
  <?php endif;?>
</head>
<body <?php body_class(); ?>>
	<div id="top"></div>
	<?php
		do_action( 'wp_body_open' );
		do_action( 'vamtam_body' );

		$slider_above_header = is_singular( VamtamFramework::$complex_layout ) && vamtam_post_meta( null, 'sticky-header-type', true ) === 'below';

		if ( $slider_above_header ) {
			get_template_part( 'templates/header/middle' );
		}

		get_template_part( 'templates/header' );
	?>
	<div id="page" class="main-container">
		<?php
			if ( ! $slider_above_header ) {
				get_template_part( 'templates/header/middle' );
			}
		?>

		<div id="main-content">
			<?php get_template_part( 'templates/header/sub-header' ); ?>

			<?php $hide_lowres_bg = vamtam_get_optionb( 'main-background-hide-lowres' ) ? 'vamtam-hide-bg-lowres' : ''; ?>
			<div id="main" role="main" class="vamtam-main layout-<?php echo esc_attr( VamtamTemplates::get_layout() ) ?>  <?php echo esc_attr( $hide_lowres_bg ) ?>">
				<?php do_action( 'vamtam_inside_main' ) ?>

				<?php if ( VamtamTemplates::had_limit_wrapper() ) : ?>
					<div class="limit-wrapper vamtam-box-outer-padding">
				<?php endif ?>
