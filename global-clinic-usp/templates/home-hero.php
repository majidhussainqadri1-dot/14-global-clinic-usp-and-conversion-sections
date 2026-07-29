<?php defined( 'ABSPATH' ) || exit; ?>
<section class="sgc-home-hero" aria-labelledby="sgc-home-title">
	<div class="sgc-hero-copy">
		<span class="sgc-eyebrow">Global Cloud Clinic Network</span>
		<h1 id="sgc-home-title">One Global Clinic Network. Independent Doctors. Worldwide Patient Access.</h1>
		<p>Build your independent global clinic presence or connect with verified homeopathic doctors from around the world.</p>
	</div>

	<div class="sgc-audience-grid">
		<article class="sgc-audience-card sgc-patient-card">
			<span class="sgc-card-label">For Patients</span>
			<h2>Find Trusted Care Beyond Local Boundaries</h2>
			<p>Review verified professional profiles and connect with suitable homeopathic doctors worldwide.</p>
			<a class="sgc-button" href="<?php echo esc_url( $doctors_url ); ?>">Find a Global Doctor</a>
		</article>

		<article class="sgc-audience-card sgc-doctor-card">
			<span class="sgc-card-label">For Doctors</span>
			<h2>Build Your Independent Global Clinic</h2>
			<p>Establish your professional clinic presence with zero platform commissions and no platform-imposed geographic limits.</p>
			<a class="sgc-button sgc-button-dark" href="<?php echo esc_url( $portal_url ); ?>">Start Your Global Clinic</a>
		</article>
	</div>

	<div class="sgc-hero-bottom">
		<p><strong>Professional independence.</strong> Responsible global discovery. Direct doctor-patient connections.</p>
		<form class="sgc-site-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="sgc-home-search">Search the website</label>
			<input id="sgc-home-search" type="search" name="s" placeholder="Search news and knowledge">
			<button type="submit">Search</button>
		</form>
	</div>
	<p class="sgc-compliance-note">Professional services remain subject to each practitioner's qualifications and applicable local laws. Platform visibility does not guarantee patient inquiries or clinical outcomes.</p>
</section>
