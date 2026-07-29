<?php defined( 'ABSPATH' ) || exit; ?>
<section class="sgc-patient-banner" aria-labelledby="<?php echo esc_attr( $title_id ); ?>">
	<div>
		<span class="sgc-eyebrow"><?php esc_html_e( 'Worldwide Patient Access', 'global-clinic-usp' ); ?></span>
		<h2 id="<?php echo esc_attr( $title_id ); ?>"><?php esc_html_e( 'Access Verified Homeopathic Doctors Beyond Local Boundaries', 'global-clinic-usp' ); ?></h2>
		<p><?php esc_html_e( 'Discover verified professional profiles from different countries, compare relevant information, and connect with a suitable practitioner.', 'global-clinic-usp' ); ?></p>
	</div>
	<?php if ( $doctors_url || $clinic_url ) : ?><div class="sgc-actions">
		<?php if ( $doctors_url ) : ?><a class="sgc-button" href="<?php echo esc_url( $doctors_url ); ?>"><?php esc_html_e( 'Search Global Doctors', 'global-clinic-usp' ); ?></a><?php endif; ?>
		<?php if ( $clinic_url ) : ?><a class="sgc-button sgc-button-outline" href="<?php echo esc_url( $clinic_url ); ?>"><?php esc_html_e( 'Explore Worldwide Clinic', 'global-clinic-usp' ); ?></a><?php endif; ?>
	</div><?php endif; ?>
	<p class="sgc-compliance-note"><?php esc_html_e( 'Verification is not an endorsement or treatment guarantee. Confirm credentials, licensing, fees, platform terms, and suitability for your location before care.', 'global-clinic-usp' ); ?></p>
</section>
