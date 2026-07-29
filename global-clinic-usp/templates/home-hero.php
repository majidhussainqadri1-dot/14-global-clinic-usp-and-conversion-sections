<?php defined( 'ABSPATH' ) || exit; ?>
<section class="sgc-home-hero" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
	<div class="sgc-hero-copy">
		<span class="sgc-eyebrow"><?php esc_html_e( 'Global Clinic Network', 'global-clinic-usp' ); ?></span>
		<h1 id="<?php echo esc_attr( $title_id ); ?>"><?php esc_html_e( 'One Global Clinic Network. Independent Doctors. Worldwide Patient Access.', 'global-clinic-usp' ); ?></h1>
		<p><?php esc_html_e( 'Build your independent global clinic presence or connect with verified homeopathic doctors from around the world.', 'global-clinic-usp' ); ?></p>
	</div>

	<div class="sgc-audience-grid">
		<article class="sgc-audience-card sgc-patient-card">
			<span class="sgc-card-label"><?php esc_html_e( 'For Patients', 'global-clinic-usp' ); ?></span>
			<h2><?php esc_html_e( 'Find Trusted Care Beyond Local Boundaries', 'global-clinic-usp' ); ?></h2>
			<p><?php esc_html_e( 'Review verified professional profiles and connect with suitable homeopathic doctors worldwide.', 'global-clinic-usp' ); ?></p>
			<?php if ( $doctors_url ) : ?><a class="sgc-button" href="<?php echo esc_url( $doctors_url ); ?>"><?php esc_html_e( 'Find a Global Doctor', 'global-clinic-usp' ); ?></a><?php endif; ?>
		</article>

		<article class="sgc-audience-card sgc-doctor-card">
			<span class="sgc-card-label"><?php esc_html_e( 'For Doctors', 'global-clinic-usp' ); ?></span>
			<h2><?php esc_html_e( 'Build Your Independent Global Clinic', 'global-clinic-usp' ); ?></h2>
			<p><?php esc_html_e( 'Establish a verified professional clinic presence with transparent platform terms and responsible worldwide discoverability.', 'global-clinic-usp' ); ?></p>
			<?php if ( $portal_url ) : ?><a class="sgc-button sgc-button-dark" href="<?php echo esc_url( $portal_url ); ?>"><?php esc_html_e( 'Start Your Global Clinic', 'global-clinic-usp' ); ?></a><?php endif; ?>
		</article>
	</div>

	<div class="sgc-hero-bottom">
		<p><strong><?php esc_html_e( 'Professional independence.', 'global-clinic-usp' ); ?></strong> <?php esc_html_e( 'Responsible global discovery. Direct doctor-patient connections.', 'global-clinic-usp' ); ?></p>
		<form class="sgc-site-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="<?php echo esc_attr( $search_id ); ?>"><?php esc_html_e( 'Search the website', 'global-clinic-usp' ); ?></label>
			<input id="<?php echo esc_attr( $search_id ); ?>" type="search" name="s" placeholder="<?php echo esc_attr__( 'Search news and knowledge', 'global-clinic-usp' ); ?>">
			<button type="submit"><?php esc_html_e( 'Search', 'global-clinic-usp' ); ?></button>
		</form>
	</div>
	<p class="sgc-compliance-note"><?php esc_html_e( 'Professional services remain subject to each practitioner’s qualifications and applicable local laws. Platform visibility does not guarantee patient inquiries, income, or clinical outcomes. Fees and platform charges, if any, are governed by the current published Business Policy.', 'global-clinic-usp' ); ?></p>
</section>
