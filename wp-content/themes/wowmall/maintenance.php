<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name=viewport content="width=device-width, initial-scale=1">
	<?php
	global $wowmall_options;
	if( ! has_site_icon() ) {
		$icon_url = apply_filters( 'get_site_icon_url', $wowmall_options['favicon']['url'], 512 );
		if( ! empty( $icon_url ) ) { ?>
			<link rel=icon href="<?php echo esc_url( $icon_url ); ?>" type=image/x-icon>
			<?php
		}
	}
	wp_head();
	$logo_url = ! empty( $wowmall_options['logo_maintenance']['url'] ) ? $wowmall_options['logo_maintenance']['url'] : '';
	$bg       = ! empty( $wowmall_options['bg_maintenance']['url'] ) ? ' style="background-image:url(' . $wowmall_options['bg_maintenance']['url'] . ')"' : '';

	if( ! empty( $wowmall_options['maintenance_demo_mode'] ) ) {
		$countdown = date( 'm/d/Y H:i:s', time() + (210 * 24 * 60 * 60) );
	} else {
		$countdown = ! empty( $wowmall_options['maintenance_date'] ) ? $wowmall_options['maintenance_date'] : '';
		$hours     = ! empty( $wowmall_options['maintenance_hours'] ) ? $wowmall_options['maintenance_hours'] : '00';
		if( ! empty( $countdown ) ) {
			$countdown .= ' ' . $hours . ':00:00';
		}
	}
	?>
</head>

<body class=body-maintenance<?php echo '' . $bg; ?>>
<?php
if( ! empty( $logo_url ) ) { ?>
	<a class=logo href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="<?php echo esc_attr( $logo_url ); ?>"></a>
	<h1><?php echo esc_html( $wowmall_options['maintenance_page_title'] ); ?></h1>
<?php
}
	if( ! empty( $countdown ) ) {
		$format = '<div><span>%m</span> ' .
		          esc_html__( 'months', 'wowmall' ) . '</div>' .
		          '<div><span>%n</span> ' .
		          esc_html__( 'days', 'wowmall' ) . '</div>' .
		          '<div><span>%H</span> ' .
		          esc_html__( 'hours', 'wowmall' ) . '</div>' .
		          '<div><span>%M</span> ' .
		          esc_html__( 'minutes', 'wowmall' ) . '</div>' .
		          '<div><span>%S</span> ' .
		          esc_html__( 'seconds', 'wowmall' ) . '</div>';
		?>
		<div id=clock data-countdown="<?php echo esc_attr( $countdown ); ?>" data-format="<?php echo esc_attr( $format ); ?>"></div>
	<?php } ?>
	<div class=maintenance-newsletter-pretext>
<?php echo wp_kses_post( $wowmall_options['maintenance_newsletter_pretext'] ); ?>
	</div>
<?php
$form_id = ! empty( $wowmall_options['maintenance_newsletter_form'] ) ? $wowmall_options['maintenance_newsletter_form'] : '';
do_action( 'init' );
wowmall()->subscribe_form( $form_id ); ?>
<div class=social-media-profiles-menu>
	<?php wp_nav_menu( array(
		'theme_location' => 'social',
		'fallback_cb'    => '__return_empty_string',
	) ); ?>
</div>
<?php wowmall()->footer_text(); ?>
<?php wp_footer(); ?>
</body>
</html>
<?php
die();
