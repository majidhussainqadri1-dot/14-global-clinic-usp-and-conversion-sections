<?php defined( 'ABSPATH' ) || exit; ?>
<section class="sgc-page sgc-mission-page" id="<?php echo esc_attr( $section_id ); ?>" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
	<header class="sgc-page-hero sgc-mission-hero">
		<div>
			<span class="sgc-eyebrow"><?php esc_html_e( 'Our Mission', 'global-clinic-usp' ); ?></span>
			<h1 id="<?php echo esc_attr( $title_id ); ?>"><?php esc_html_e( 'A More Independent and Connected Future for Homeopathy', 'global-clinic-usp' ); ?></h1>
			<p><?php esc_html_e( 'Sabri Homeopathy is building a responsible global clinic network centered on professional independence for doctors and meaningful worldwide access for patients.', 'global-clinic-usp' ); ?></p>
		</div>
	</header>

	<section class="sgc-mission-grid" aria-label="<?php echo esc_attr__( 'Mission principles', 'global-clinic-usp' ); ?>">
		<article><span>01</span><h2><?php esc_html_e( 'The Common Limitation', 'global-clinic-usp' ); ?></h2><p><?php esc_html_e( 'Some digital platforms limit professional visibility through geographic restrictions, closed referral systems, unclear charges, or restricted access to patients.', 'global-clinic-usp' ); ?></p></article>
		<article><span>02</span><h2><?php esc_html_e( 'Our Alternative', 'global-clinic-usp' ); ?></h2><p><?php esc_html_e( 'We are creating an open professional network where verified doctors can establish an independent clinic presence and patients can search beyond local boundaries.', 'global-clinic-usp' ); ?></p></article>
		<article><span>03</span><h2><?php esc_html_e( 'Our Responsibility', 'global-clinic-usp' ); ?></h2><p><?php esc_html_e( 'Global access must remain transparent and responsible. Verification, privacy, professional standards, published business terms, and applicable laws remain essential.', 'global-clinic-usp' ); ?></p></article>
	</section>

	<section class="sgc-vision-statement">
		<span class="sgc-eyebrow"><?php esc_html_e( 'The Vision', 'global-clinic-usp' ); ?></span>
		<h2><?php esc_html_e( 'We envision a new era of professional independence, global collaboration, and responsible digital transformation in homeopathy.', 'global-clinic-usp' ); ?></h2>
		<p><?php esc_html_e( 'Our goal is not to control a doctor’s clinic. It is to provide a governed structure through which qualified professionals can present their work, build trust, and connect with patients responsibly.', 'global-clinic-usp' ); ?></p>
	</section>

	<section class="sgc-final-cta">
		<div><h2><?php esc_html_e( 'Choose Your Path', 'global-clinic-usp' ); ?></h2><p><?php esc_html_e( 'Build your professional clinic presence or discover verified doctors through the global network.', 'global-clinic-usp' ); ?></p></div>
		<?php if ( $portal_url || $doctors_url ) : ?><div class="sgc-actions"><?php if ( $portal_url ) : ?><a class="sgc-button" href="<?php echo esc_url( $portal_url ); ?>"><?php esc_html_e( 'Explore Doctor Portal', 'global-clinic-usp' ); ?></a><?php endif; ?><?php if ( $doctors_url ) : ?><a class="sgc-button sgc-button-outline" href="<?php echo esc_url( $doctors_url ); ?>"><?php esc_html_e( 'Find a Global Doctor', 'global-clinic-usp' ); ?></a><?php endif; ?></div><?php endif; ?>
	</section>
</section>
