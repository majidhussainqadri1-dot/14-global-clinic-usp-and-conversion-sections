<?php defined( 'ABSPATH' ) || exit; ?>
<section class="sgc-page sgc-doctor-portal" id="<?php echo esc_attr( $section_id ); ?>">
	<header class="sgc-page-hero">
		<div>
			<span class="sgc-eyebrow"><?php esc_html_e( 'For Homeopathic Doctors Worldwide', 'global-clinic-usp' ); ?></span>
			<h1><?php esc_html_e( 'Build Your Independent Global Clinic Presence', 'global-clinic-usp' ); ?></h1>
			<p><?php esc_html_e( 'Create a trusted professional presence, connect directly with patients, and use available doctor features under transparent platform terms and applicable laws.', 'global-clinic-usp' ); ?></p>
			<?php if ( $application_url || $clinic_url ) : ?>
				<div class="sgc-actions">
					<?php if ( $application_url ) : ?><a class="sgc-button" href="<?php echo esc_url( $application_url ); ?>"><?php esc_html_e( 'Create Your Doctor Profile', 'global-clinic-usp' ); ?></a><?php endif; ?>
					<?php if ( $clinic_url ) : ?><a class="sgc-button sgc-button-outline" href="<?php echo esc_url( $clinic_url ); ?>"><?php esc_html_e( 'Explore Worldwide Clinic', 'global-clinic-usp' ); ?></a><?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<aside class="sgc-promise-card" aria-label="<?php echo esc_attr__( 'Platform commitments', 'global-clinic-usp' ); ?>">
			<span><?php esc_html_e( 'Our Platform Commitment', 'global-clinic-usp' ); ?></span>
			<strong><?php esc_html_e( 'Transparent Published Terms', 'global-clinic-usp' ); ?></strong>
			<strong><?php esc_html_e( 'No Arbitrary Country-Based Access Limits', 'global-clinic-usp' ); ?></strong>
			<strong><?php esc_html_e( 'Independent Professional Identity', 'global-clinic-usp' ); ?></strong>
		</aside>
	</header>

	<section class="sgc-section" aria-labelledby="<?php echo esc_attr( $benefits_id ); ?>">
		<div class="sgc-section-head">
			<span class="sgc-eyebrow"><?php esc_html_e( 'Professional Freedom', 'global-clinic-usp' ); ?></span>
			<h2 id="<?php echo esc_attr( $benefits_id ); ?>"><?php esc_html_e( 'A Global Platform Designed Around Responsible Independence', 'global-clinic-usp' ); ?></h2>
		</div>
		<div class="sgc-benefit-grid">
			<article><span>01</span><h3><?php esc_html_e( 'Global Patient Visibility', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Present your professional services beyond your home country and become discoverable to patients seeking suitable care internationally.', 'global-clinic-usp' ); ?></p></article>
			<article><span>02</span><h3><?php esc_html_e( 'Access to Available Doctor Features', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Doctors who meet verification and eligibility requirements can use the doctor features currently available to their role.', 'global-clinic-usp' ); ?></p></article>
			<article><span>03</span><h3><?php esc_html_e( 'Transparent Business Terms', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Any platform fee, commission, payment, receipt, refund, or payout rule must follow the single published Business Policy then in force.', 'global-clinic-usp' ); ?></p></article>
			<article><span>04</span><h3><?php esc_html_e( 'Your Professional Fees', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Set professional fees and accepted payment methods subject to the published platform policy, applicable laws, and professional obligations.', 'global-clinic-usp' ); ?></p></article>
			<article><span>05</span><h3><?php esc_html_e( 'Organic Discoverability', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Structured profiles support responsible search visibility without guaranteeing rankings, inquiries, patients, or income.', 'global-clinic-usp' ); ?></p></article>
			<article><span>06</span><h3><?php esc_html_e( 'Independent Clinic Presence', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Your profile can present credentials, services, contact options, availability, and patient access in one professional location.', 'global-clinic-usp' ); ?></p></article>
		</div>
	</section>

	<section class="sgc-section sgc-how-it-works" aria-labelledby="<?php echo esc_attr( $process_id ); ?>">
		<div class="sgc-section-head"><span class="sgc-eyebrow"><?php esc_html_e( 'How It Works', 'global-clinic-usp' ); ?></span><h2 id="<?php echo esc_attr( $process_id ); ?>"><?php esc_html_e( 'From Application to Global Clinic Presence', 'global-clinic-usp' ); ?></h2></div>
		<div class="sgc-steps">
			<article><b>1</b><h3><?php esc_html_e( 'Create Your Profile', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Submit your professional identity, qualifications, experience, languages, services, and contact information.', 'global-clinic-usp' ); ?></p></article>
			<article><b>2</b><h3><?php esc_html_e( 'Complete Verification', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Provide the required professional documents. Verified status is displayed only after authorized review is completed.', 'global-clinic-usp' ); ?></p></article>
			<article><b>3</b><h3><?php esc_html_e( 'Open Your Global Clinic', 'global-clinic-usp' ); ?></h3><p><?php esc_html_e( 'Publish permitted clinic information and connect with patients who discover your verified profile.', 'global-clinic-usp' ); ?></p></article>
		</div>
	</section>

	<section class="sgc-final-cta">
		<div><span class="sgc-eyebrow"><?php esc_html_e( 'Your Profession. Your Identity. Your Clinic.', 'global-clinic-usp' ); ?></span><h2><?php esc_html_e( 'Start Building Your Global Clinic Presence', 'global-clinic-usp' ); ?></h2><p><?php esc_html_e( 'Join a network designed for professional independence and responsible worldwide patient access.', 'global-clinic-usp' ); ?></p></div>
		<?php if ( $application_url || $doctors_url ) : ?><div class="sgc-actions"><?php if ( $application_url ) : ?><a class="sgc-button" href="<?php echo esc_url( $application_url ); ?>"><?php esc_html_e( 'Start Doctor Application', 'global-clinic-usp' ); ?></a><?php endif; ?><?php if ( $doctors_url ) : ?><a class="sgc-text-link" href="<?php echo esc_url( $doctors_url ); ?>"><?php esc_html_e( 'View the Doctor Directory', 'global-clinic-usp' ); ?></a><?php endif; ?></div><?php endif; ?>
	</section>

	<p class="sgc-legal-note"><?php esc_html_e( 'Platform access, professional visibility, international patient contact, fees, and payments do not override licensing rules, telehealth requirements, tax obligations, payment regulations, or other applicable laws. Sabri Homeopathy does not guarantee search rankings, patient inquiries, income, or clinical outcomes.', 'global-clinic-usp' ); ?></p>
</section>
