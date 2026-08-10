<?php

defined( 'ABSPATH' ) || exit;

/**
 * Pure policy and validation helpers. This class intentionally owns no companion data.
 */
final class GCU_Policy {
	const COPY_REVIEW_DAYS = 365;
	const ATTRIBUTION_TTL  = 2592000; // 30 days.
	const EVENT_TOKEN_TTL  = 900;
	const MAX_TEXT_LENGTH  = 5000;
	const EVENT_RATE_LIMIT = 60;

	public static function business_rules() {
		return array(
			'platform_commission_percent' => 0,
			'approved_core_tier'           => 'free',
			'optional_support'             => true,
			'support_affects_visibility'   => false,
			'instant_doctor_approval'       => false,
			'cure_guarantee'                => false,
		);
	}

	public static function supported_locales() {
		return array( 'en-US', 'ur-PK', 'ar-SA' );
	}

	public static function canonical_claims() {
		$now = time();
		return array(
			'zero_platform_commission' => array(
				'text'       => 'The platform charges 0% commission on approved clinic transactions.',
				'basis'      => 'Founder-approved platform financial policy, 2026-08-04',
				'owner'      => 'Founder / business policy owner',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
			'free_approved_core' => array(
				'text'       => 'All currently approved core platform features are available in one free tier.',
				'basis'      => 'Founder-approved Single Free Tier — Beyond-Pro policy, 2026-08-04',
				'owner'      => 'Founder / entitlement owner',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
			'optional_support_no_ranking' => array(
				'text'       => 'Voluntary support is optional and does not purchase ranking, visibility, verification or basic service.',
				'basis'      => 'Founder-approved donation and fairness policy, 2026-08-04',
				'owner'      => 'Founder / transparency owner',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
			'verification_required' => array(
				'text'       => 'Doctor access is activated only after identity, professional evidence, duplicate and risk checks, and the required review are completed.',
				'basis'      => 'High-Trust Verified-Entry Membership Principle, 2026-08-05',
				'owner'      => 'File 09 / File 00',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
			'no_verification_guarantee' => array(
				'text'       => 'Starting an application does not guarantee verification or activation.',
				'basis'      => 'File 09 verification boundary',
				'owner'      => 'File 09',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
			'no_emergency_service' => array(
				'text'       => 'This platform is not an emergency service. Seek immediate local emergency care for urgent or life-threatening symptoms.',
				'basis'      => 'Clinical safety boundary',
				'owner'      => 'File 08 / clinical governance',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
			'no_cure_guarantee' => array(
				'text'       => 'Verification is not an endorsement or guarantee of a cure, income or outcome.',
				'basis'      => 'Ethical and medical claim boundary',
				'owner'      => 'Founder / File 24 assurance',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
			'direct_fee_flow' => array(
				'text'       => 'Any doctor fee is shown and handled by the canonical clinic or approved provider flow; File 14 does not collect, alter or guarantee payment.',
				'basis'      => 'File 08 / financial-owner boundary',
				'owner'      => 'File 08 / approved payment owner',
				'effective'  => $now,
				'review_due' => $now + ( self::COPY_REVIEW_DAYS * DAY_IN_SECONDS ),
				'public'     => true,
			),
		);
	}

	/**
	 * Seeded content is stored as versioned records. English is canonical; Urdu and Arabic
	 * are approved locale variants with the same semantic block identity.
	 */
	public static function canonical_blocks( $locale = 'en-US' ) {
		$locale = self::sanitize_locale( $locale );
		$sets   = self::canonical_block_sets();
		return isset( $sets[ $locale ] ) ? $sets[ $locale ] : $sets['en-US'];
	}

	public static function canonical_block_sets() {
		$en = array(
			'patient_hero' => array(
				'audience' => 'patient', 'type' => 'hero', 'slot' => 'global_clinic_primary',
				'title' => 'Find a Verified Global Homeopathic Doctor',
				'body' => 'Search approved public doctor profiles by country, language, consultation mode and professional scope, then continue to the doctor or clinic owner for availability and booking.',
				'cta_label' => 'Find a Global Doctor', 'destination' => 'doctor_directory',
				'claim_keys' => array( 'no_emergency_service', 'no_cure_guarantee' ),
			),
			'doctor_hero' => array(
				'audience' => 'doctor', 'type' => 'hero', 'slot' => 'global_clinic_primary',
				'title' => 'Start Your Global Clinic',
				'body' => 'Build a personal-site-like professional presence with a verified profile, clinic identity, posts, media, appointments and secure communication after the required approval process.',
				'cta_label' => 'Start Your Global Clinic', 'destination' => 'doctor_onboarding',
				'claim_keys' => array( 'zero_platform_commission', 'free_approved_core', 'optional_support_no_ranking', 'verification_required', 'no_verification_guarantee', 'direct_fee_flow' ),
			),
			'trust' => array(
				'audience' => 'all', 'type' => 'trust', 'slot' => 'global_clinic_trust',
				'title' => 'High-Trust Entry, Honest Limits',
				'body' => 'Public information may be read without an account. Protected actions require an approved account, and sensitive professional capabilities require the relevant verification and authorization.',
				'cta_label' => '', 'destination' => '',
				'claim_keys' => array( 'verification_required', 'no_verification_guarantee', 'no_cure_guarantee' ),
			),
			'how_it_works' => array(
				'audience' => 'all', 'type' => 'steps', 'slot' => 'global_clinic_steps',
				'title' => 'How the Global Clinic Journey Works',
				'body' => 'Patients discover a verified doctor, review the canonical public profile or clinic, and use the appointment owner for booking. Doctors create an account, submit professional evidence, complete review, then configure the profile and clinic owners.',
				'cta_label' => 'Read the Full Process', 'destination' => 'how_it_works',
				'claim_keys' => array( 'verification_required', 'no_verification_guarantee' ),
			),
			'faq_approval' => array(
				'audience' => 'all', 'type' => 'faq', 'slot' => 'global_clinic_faq',
				'title' => 'Does starting a doctor application guarantee approval?',
				'body' => 'No. Identity, professional evidence, duplicate and risk checks, rule acceptance and the required review must be completed.',
				'cta_label' => '', 'destination' => '', 'claim_keys' => array( 'verification_required', 'no_verification_guarantee' ),
			),
			'faq_commission' => array(
				'audience' => 'all', 'type' => 'faq', 'slot' => 'global_clinic_faq',
				'title' => 'Does the platform charge a clinic commission?',
				'body' => 'The approved policy is 0% platform commission. Voluntary support is separate and does not purchase ranking, visibility, verification or basic service.',
				'cta_label' => '', 'destination' => '', 'claim_keys' => array( 'zero_platform_commission', 'optional_support_no_ranking', 'direct_fee_flow' ),
			),
			'faq_clinical' => array(
				'audience' => 'all', 'type' => 'faq', 'slot' => 'global_clinic_faq',
				'title' => 'Can this page diagnose or prescribe?',
				'body' => 'No. File 14 only explains and connects approved journeys. Clinical decisions remain with the qualified clinician and the relevant clinical owner.',
				'cta_label' => '', 'destination' => '', 'claim_keys' => array( 'no_emergency_service' ),
			),
			'faq_outcome' => array(
				'audience' => 'all', 'type' => 'faq', 'slot' => 'global_clinic_faq',
				'title' => 'Are verified doctors guaranteed to cure a condition?',
				'body' => 'No. Verification confirms the approved evidence and review state; it is not a cure, income or outcome guarantee.',
				'cta_label' => '', 'destination' => '', 'claim_keys' => array( 'no_cure_guarantee' ),
			),
		);

		$ur = array(
			'patient_hero' => array( 'audience'=>'patient','type'=>'hero','slot'=>'global_clinic_primary','title'=>'تصدیق شدہ عالمی ہومیوپیتھک ڈاکٹر تلاش کریں','body'=>'ملک، زبان، مشاورت کے طریقے اور پیشہ ورانہ دائرۂ کار کے مطابق منظور شدہ عوامی ڈاکٹر پروفائل تلاش کریں، پھر دستیابی اور وقت لینے کے لیے متعلقہ ڈاکٹر یا کلینک کے اصل نظام تک جائیں۔','cta_label'=>'عالمی ڈاکٹر تلاش کریں','destination'=>'doctor_directory','claim_keys'=>array('no_emergency_service','no_cure_guarantee') ),
			'doctor_hero' => array( 'audience'=>'doctor','type'=>'hero','slot'=>'global_clinic_primary','title'=>'اپنا عالمی کلینک شروع کریں','body'=>'مطلوبہ منظوری کے بعد تصدیق شدہ پروفائل، کلینک شناخت، تحریری و بصری مواد، ملاقاتوں اور محفوظ رابطے کے ساتھ ذاتی ویب سائٹ جیسی پیشہ ورانہ موجودگی قائم کریں۔','cta_label'=>'اپنا عالمی کلینک شروع کریں','destination'=>'doctor_onboarding','claim_keys'=>array('zero_platform_commission','free_approved_core','optional_support_no_ranking','verification_required','no_verification_guarantee','direct_fee_flow') ),
			'trust' => array( 'audience'=>'all','type'=>'trust','slot'=>'global_clinic_trust','title'=>'اعلیٰ اعتماد کے ساتھ داخلہ، حدود کا دیانت دارانہ بیان','body'=>'عوامی معلومات اکاؤنٹ کے بغیر پڑھی جاسکتی ہیں۔ محفوظ افعال کے لیے منظور شدہ اکاؤنٹ اور حساس پیشہ ورانہ سہولیات کے لیے متعلقہ تصدیق و اجازت لازم ہے۔','cta_label'=>'','destination'=>'','claim_keys'=>array('verification_required','no_verification_guarantee','no_cure_guarantee') ),
			'how_it_works' => array( 'audience'=>'all','type'=>'steps','slot'=>'global_clinic_steps','title'=>'عالمی کلینک کا سفر کس طرح مکمل ہوتا ہے؟','body'=>'مریض تصدیق شدہ ڈاکٹر تلاش کرتا، اصل عوامی پروفائل یا کلینک دیکھتا اور ملاقات کے اصل نظام سے وقت لیتا ہے۔ ڈاکٹر اکاؤنٹ بناتا، پیشہ ورانہ شواہد جمع کرتا، جانچ مکمل کرتا، پھر اصل پروفائل اور کلینک نظام میں اپنی معلومات قائم کرتا ہے۔','cta_label'=>'مکمل طریقۂ کار پڑھیں','destination'=>'how_it_works','claim_keys'=>array('verification_required','no_verification_guarantee') ),
			'faq_approval' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'کیا ڈاکٹر کی درخواست شروع کرنے سے منظوری یقینی ہوجاتی ہے؟','body'=>'نہیں۔ شناخت، پیشہ ورانہ شواہد، تکرار و خطرے کی جانچ، قواعد کی قبولیت اور مطلوبہ انسانی نظرِ ثانی مکمل ہونا لازم ہے۔','cta_label'=>'','destination'=>'','claim_keys'=>array('verification_required','no_verification_guarantee') ),
			'faq_commission' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'کیا پلیٹ فارم کلینک کمیشن لیتا ہے؟','body'=>'منظور شدہ پالیسی کے مطابق پلیٹ فارم کمیشن صفر فیصد ہے۔ رضاکارانہ تعاون الگ ہے اور اس سے درجہ بندی، نمائش، تصدیق یا بنیادی خدمت نہیں خریدی جاسکتی۔','cta_label'=>'','destination'=>'','claim_keys'=>array('zero_platform_commission','optional_support_no_ranking','direct_fee_flow') ),
			'faq_clinical' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'کیا یہ صفحہ تشخیص یا نسخہ تجویز کرسکتا ہے؟','body'=>'نہیں۔ فائل 14 صرف منظور شدہ راستوں کی وضاحت اور ربط کرتی ہے۔ طبی فیصلہ مستند معالج اور متعلقہ طبی نظام ہی کے پاس رہے گا۔','cta_label'=>'','destination'=>'','claim_keys'=>array('no_emergency_service') ),
			'faq_outcome' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'کیا تصدیق شدہ ڈاکٹر سے شفا کی ضمانت ملتی ہے؟','body'=>'نہیں۔ تصدیق صرف منظور شدہ شواہد اور جانچ کی حالت بتاتی ہے؛ یہ شفا، آمدن یا نتیجے کی ضمانت نہیں۔','cta_label'=>'','destination'=>'','claim_keys'=>array('no_cure_guarantee') ),
		);

		$ar = array(
			'patient_hero' => array( 'audience'=>'patient','type'=>'hero','slot'=>'global_clinic_primary','title'=>'ابحث عن طبيب هوميوباثي عالمي موثَّق','body'=>'ابحث في الملفات العامة المعتمدة حسب البلد واللغة وطريقة الاستشارة والنطاق المهني، ثم انتقل إلى المالك الأصلي لملف الطبيب أو العيادة لمعرفة التوفر والحجز.','cta_label'=>'ابحث عن طبيب عالمي','destination'=>'doctor_directory','claim_keys'=>array('no_emergency_service','no_cure_guarantee') ),
			'doctor_hero' => array( 'audience'=>'doctor','type'=>'hero','slot'=>'global_clinic_primary','title'=>'ابدأ عيادتك العالمية','body'=>'أنشئ حضوراً مهنياً شبيهاً بالموقع الشخصي، مع ملف موثَّق وهوية عيادة ومحتوى ومواعيد واتصال آمن بعد إتمام مسار الموافقة المطلوب.','cta_label'=>'ابدأ عيادتك العالمية','destination'=>'doctor_onboarding','claim_keys'=>array('zero_platform_commission','free_approved_core','optional_support_no_ranking','verification_required','no_verification_guarantee','direct_fee_flow') ),
			'trust' => array( 'audience'=>'all','type'=>'trust','slot'=>'global_clinic_trust','title'=>'دخول عالي الثقة وحدود صريحة','body'=>'يمكن قراءة المعلومات العامة بلا حساب. تتطلب الإجراءات المحمية حساباً معتمداً، وتتطلب القدرات المهنية الحساسة التحقق والتفويض المناسبين.','cta_label'=>'','destination'=>'','claim_keys'=>array('verification_required','no_verification_guarantee','no_cure_guarantee') ),
			'how_it_works' => array( 'audience'=>'all','type'=>'steps','slot'=>'global_clinic_steps','title'=>'كيف تعمل رحلة العيادة العالمية؟','body'=>'يكتشف المريض طبيباً موثَّقاً، ويراجع الملف العام أو العيادة الأصلية، ثم يستخدم مالك المواعيد للحجز. وينشئ الطبيب حساباً، ويقدم الأدلة المهنية، ويتمم المراجعة، ثم يضبط ملفه وعيادته لدى المالكين الأصليين.','cta_label'=>'اقرأ العملية كاملة','destination'=>'how_it_works','claim_keys'=>array('verification_required','no_verification_guarantee') ),
			'faq_approval' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'هل بدء طلب الطبيب يضمن الموافقة؟','body'=>'لا. يجب إتمام التحقق من الهوية والأدلة المهنية وفحوص التكرار والمخاطر وقبول القواعد والمراجعة المطلوبة.','cta_label'=>'','destination'=>'','claim_keys'=>array('verification_required','no_verification_guarantee') ),
			'faq_commission' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'هل تفرض المنصة عمولة على العيادة؟','body'=>'السياسة المعتمدة هي عمولة منصة قدرها 0%. الدعم التطوعي منفصل ولا يشتري الترتيب أو الظهور أو التحقق أو الخدمة الأساسية.','cta_label'=>'','destination'=>'','claim_keys'=>array('zero_platform_commission','optional_support_no_ranking','direct_fee_flow') ),
			'faq_clinical' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'هل يمكن لهذه الصفحة التشخيص أو وصف العلاج؟','body'=>'لا. تشرح File 14 المسارات المعتمدة وتربط بينها فقط. تبقى القرارات السريرية للطبيب المؤهل والمالك السريري المختص.','cta_label'=>'','destination'=>'','claim_keys'=>array('no_emergency_service') ),
			'faq_outcome' => array( 'audience'=>'all','type'=>'faq','slot'=>'global_clinic_faq','title'=>'هل يضمن الطبيب الموثَّق الشفاء؟','body'=>'لا. يثبت التحقق حالة الأدلة والمراجعة المعتمدة؛ ولا يضمن الشفاء أو الدخل أو النتيجة.','cta_label'=>'','destination'=>'','claim_keys'=>array('no_cure_guarantee') ),
		);

		return array( 'en-US' => $en, 'ur-PK' => $ur, 'ar-SA' => $ar );
	}

	public static function localized_claim_text( $claim_key, $locale, $default ) {
		$locale = self::sanitize_locale( $locale );
		$translations = array(
			'ur-PK' => array(
				'zero_platform_commission' => 'منظور شدہ کلینک لین دین پر پلیٹ فارم کمیشن صفر فیصد ہے۔',
				'free_approved_core' => 'اس وقت منظور شدہ تمام بنیادی پلیٹ فارم سہولیات ایک ہی مفت درجے میں دستیاب ہیں۔',
				'optional_support_no_ranking' => 'رضاکارانہ تعاون اختیاری ہے اور اس سے درجہ بندی، نمائش، تصدیق یا بنیادی خدمت نہیں خریدی جاسکتی۔',
				'verification_required' => 'ڈاکٹر کی سہولت شناخت، پیشہ ورانہ شواہد، تکرار و خطرے کی جانچ اور مطلوبہ نظرِ ثانی مکمل ہونے کے بعد ہی فعال ہوتی ہے۔',
				'no_verification_guarantee' => 'درخواست شروع کرنا تصدیق یا فعالیت کی ضمانت نہیں۔',
				'no_emergency_service' => 'یہ پلیٹ فارم ہنگامی خدمت نہیں؛ فوری یا جان لیوا علامات میں مقامی ہنگامی طبی مدد حاصل کریں۔',
				'no_cure_guarantee' => 'تصدیق شفا، آمدن یا نتیجے کی توثیق یا ضمانت نہیں۔',
				'direct_fee_flow' => 'ڈاکٹر کی فیس متعلقہ کلینک یا منظور شدہ ادائیگی نظام میں دکھائی اور وصول کی جاتی ہے؛ فائل 14 ادائیگی وصول یا تبدیل نہیں کرتی۔',
			),
			'ar-SA' => array(
				'zero_platform_commission' => 'تبلغ عمولة المنصة على معاملات العيادة المعتمدة 0%.',
				'free_approved_core' => 'تتوفر جميع الميزات الأساسية المعتمدة حالياً ضمن مستوى مجاني واحد.',
				'optional_support_no_ranking' => 'الدعم التطوعي اختياري ولا يشتري الترتيب أو الظهور أو التحقق أو الخدمة الأساسية.',
				'verification_required' => 'لا تُفعَّل قدرات الطبيب إلا بعد إتمام الهوية والأدلة المهنية وفحوص التكرار والمخاطر والمراجعة المطلوبة.',
				'no_verification_guarantee' => 'بدء الطلب لا يضمن التحقق أو التفعيل.',
				'no_emergency_service' => 'هذه المنصة ليست خدمة طوارئ؛ اطلب الرعاية المحلية العاجلة للأعراض الخطرة أو المهددة للحياة.',
				'no_cure_guarantee' => 'التحقق ليس تزكية ولا ضماناً للشفاء أو الدخل أو النتيجة.',
				'direct_fee_flow' => 'تُعرض رسوم الطبيب وتُدار لدى العيادة الأصلية أو مزود الدفع المعتمد؛ ولا تجمع File 14 المدفوعات أو تعدلها.',
			),
		);
		return isset( $translations[ $locale ][ $claim_key ] ) ? $translations[ $locale ][ $claim_key ] : $default;
	}

	public static function copy_transitions() {
		return array(
			'draft' => array( 'policy_review', 'withdrawn' ),
			'policy_review' => array( 'founder_approved', 'draft', 'withdrawn' ),
			'founder_approved' => array( 'active', 'withdrawn' ),
			'active' => array( 'superseded', 'withdrawn' ),
			'superseded' => array(),
			'withdrawn' => array(),
		);
	}

	public static function placement_transitions() {
		return array(
			'planned' => array( 'preview', 'paused', 'expired' ),
			'preview' => array( 'active', 'planned', 'paused' ),
			'active' => array( 'paused', 'expired' ),
			'paused' => array( 'preview', 'expired' ),
			'expired' => array(),
		);
	}

	public static function experiment_transitions() {
		return array(
			'proposed' => array( 'approved', 'rejected' ),
			'approved' => array( 'running', 'stopped' ),
			'running' => array( 'stopped' ),
			'stopped' => array( 'analyzed' ),
			'analyzed' => array( 'adopted', 'rejected' ),
			'adopted' => array(),
			'rejected' => array(),
		);
	}

	public static function transition_allowed( $machine, $from, $to ) {
		$maps = array( 'copy'=>self::copy_transitions(), 'placement'=>self::placement_transitions(), 'experiment'=>self::experiment_transitions() );
		return isset( $maps[ $machine ][ $from ] ) && in_array( $to, $maps[ $machine ][ $from ], true );
	}

	public static function sanitize_locale( $locale ) {
		$locale = str_replace( '_', '-', trim( sanitize_text_field( (string) $locale ) ) );
		$lookup = array(
			'en'    => 'en-US',
			'en-us' => 'en-US',
			'ur'    => 'ur-PK',
			'ur-pk' => 'ur-PK',
			'ar'    => 'ar-SA',
			'ar-sa' => 'ar-SA',
		);
		$key = strtolower( $locale );
		return isset( $lookup[ $key ] ) ? $lookup[ $key ] : 'en-US';
	}

	public static function sanitize_audience( $audience ) {
		$allowed = array( 'all', 'patient', 'doctor', 'guest', 'member' );
		$audience = sanitize_key( (string) $audience );
		return in_array( $audience, $allowed, true ) ? $audience : 'all';
	}

	public static function sanitize_campaign( array $input ) {
		$out = array();
		foreach ( array( 'source', 'medium', 'campaign', 'ref' ) as $key ) {
			$value = isset( $input[ $key ] ) ? sanitize_text_field( wp_unslash( $input[ $key ] ) ) : '';
			$value = function_exists( 'mb_substr' ) ? mb_substr( $value, 0, 100 ) : substr( $value, 0, 100 );
			$out[ $key ] = self::campaign_value_is_sensitive( $value ) ? '' : $value;
		}
		return $out;
	}

	public static function campaign_value_is_sensitive( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) { return false; }
		if ( preg_match( '/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $value ) ) { return true; }
		if ( preg_match( '/(?:\+?\d[\s().\-]*){7,}/', $value ) ) { return true; }
		$lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $value ) : strtolower( $value );
		$markers = array( 'cnic','passport','patient id','medical record','diagnosis','symptom','prescription','email','phone','mobile','شناختی','شناختی کارڈ','پاسپورٹ','مریض','تشخیص','علامت','نسخ','ای میل','فون','موبائل','هوية','جواز','مريض','تشخيص','عرض','وصفة','بريد','هاتف','جوال' );
		foreach ( $markers as $marker ) { if ( false !== strpos( $lower, $marker ) ) { return true; } }
		return false;
	}

	public static function analytics_consent() {
		$consent = isset( $_COOKIE['gcu_measurement_consent'] ) && 'yes' === sanitize_key( wp_unslash( $_COOKIE['gcu_measurement_consent'] ) );
		return (bool) apply_filters( 'gcu_analytics_consent', $consent );
	}

	public static function same_origin_url( $url ) {
		$url = esc_url_raw( (string) $url );
		if ( '' === $url ) {
			return '';
		}
		$home_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
		$url_host  = wp_parse_url( $url, PHP_URL_HOST );
		if ( $url_host && $home_host && strtolower( $url_host ) !== strtolower( $home_host ) ) {
			return '';
		}
		return $url;
	}

	public static function trace_id() {
		return 'gcu-' . wp_generate_uuid4();
	}
}
