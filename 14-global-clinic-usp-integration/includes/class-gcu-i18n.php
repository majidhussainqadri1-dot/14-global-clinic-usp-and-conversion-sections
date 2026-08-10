<?php

defined( 'ABSPATH' ) || exit;

/**
 * File 14-owned interface copy.
 *
 * English (en-US) is canonical. Urdu and Arabic must contain exactly the same
 * keys so a partially translated File 14 chrome cannot silently ship.
 */
final class GCU_I18n {
	public static function supported_locales() {
		return array( 'en-US', 'ur-PK', 'ar-SA' );
	}

	public static function current_locale() {
		$locale = function_exists( 'determine_locale' ) ? determine_locale() : get_locale();
		$map = array(
			'en_US' => 'en-US',
			'en-US' => 'en-US',
			'ur'    => 'ur-PK',
			'ur_PK' => 'ur-PK',
			'ur-PK' => 'ur-PK',
			'ar'    => 'ar-SA',
			'ar_SA' => 'ar-SA',
			'ar-SA' => 'ar-SA',
		);
		return isset( $map[ $locale ] ) ? $map[ $locale ] : 'en-US';
	}

	public static function direction( $locale = '' ) {
		$locale = $locale ? $locale : self::current_locale();
		return in_array( $locale, array( 'ur-PK', 'ar-SA' ), true ) ? 'rtl' : 'ltr';
	}

	public static function language( $locale = '' ) {
		$locale = $locale ? $locale : self::current_locale();
		$map = array( 'en-US' => 'en', 'ur-PK' => 'ur', 'ar-SA' => 'ar' );
		return isset( $map[ $locale ] ) ? $map[ $locale ] : 'en';
	}

	public static function text( $key, $locale = '' ) {
		$locale = $locale ? $locale : self::current_locale();
		$sets   = self::sets();
		$value  = isset( $sets[ $locale ][ $key ] ) ? $sets[ $locale ][ $key ] : ( isset( $sets['en-US'][ $key ] ) ? $sets['en-US'][ $key ] : $key );
		return (string) apply_filters( 'gcu_ui_text', $value, $key, $locale );
	}

	public static function missing_keys() {
		$sets     = self::sets();
		$required = array_keys( $sets['en-US'] );
		$missing  = array();
		foreach ( self::supported_locales() as $locale ) {
			$locale_keys = isset( $sets[ $locale ] ) ? array_keys( $sets[ $locale ] ) : array();
			$delta       = array_values( array_diff( $required, $locale_keys ) );
			$extra       = array_values( array_diff( $locale_keys, $required ) );
			$blank       = array();
			foreach ( $required as $key ) {
				if ( isset( $sets[ $locale ][ $key ] ) && '' === trim( (string) $sets[ $locale ][ $key ] ) ) {
					$blank[] = $key;
				}
			}
			if ( $delta || $extra || $blank ) {
				$missing[ $locale ] = array( 'missing' => $delta, 'extra' => $extra, 'blank' => $blank );
			}
		}
		return $missing;
	}

	public static function is_complete() {
		return empty( self::missing_keys() );
	}

	private static function sets() {
		return array(
			'en-US' => array(
				'worldwide_clinic'            => 'Worldwide Clinic',
				'hero_title'                  => 'Global Homeopathic Care and Professional Presence — Connected with Trust',
				'hero_body'                   => 'One clear entry point for patients seeking verified doctors and for qualified doctors beginning the approval journey for a worldwide clinic.',
				'transparent_process'         => 'Transparent Process',
				'how_title'                   => 'How the Global Clinic Journey Works',
				'journeys_label'              => 'Patient and doctor journeys',
				'for_patients'                => 'For Patients',
				'patient_step_1'              => 'Search the verified public directory.',
				'patient_step_2'              => 'Review the canonical doctor profile and clinic information.',
				'patient_step_3'              => 'Sign in for protected contact, save or appointment actions.',
				'patient_step_4'              => 'Use the clinic owner for availability, consent and booking status.',
				'for_doctors'                 => 'For Doctors',
				'doctor_step_1'               => 'Create a high-trust account and accept platform rules.',
				'doctor_step_2'               => 'Submit identity and professional evidence to File 09.',
				'doctor_step_3'               => 'Complete review, additional-information or appeal steps where required.',
				'doctor_step_4'               => 'After approval, configure the canonical profile and clinic owners.',
				'find_doctor'                 => 'Find a Global Doctor',
				'start_clinic'                => 'Start Your Global Clinic',
				'content_review_title'        => 'Content is being reviewed',
				'content_review_body'         => 'The approved version is temporarily unavailable. Please use Home or return later.',
				'destination_unavailable'     => 'This destination is temporarily unavailable. No application, booking or approval has been inferred.',
				'trust_policy_details'        => 'Trust and policy details',
				'faq_title'                   => 'Frequently Asked Questions',
				'owner_unavailable'           => 'Owner destination unavailable; no action was created.',
				'service_unavailable'         => 'Service temporarily unavailable',
				'degraded_body'               => 'The requested owner destination is unavailable or incompatible. No booking, application, verification or clinical action has been created.',
				'emergency_title'             => 'Emergency limitation',
				'emergency_body'              => 'This platform is not an emergency service. For urgent or life-threatening symptoms, contact local emergency services immediately.',
				'page_navigation'             => 'Page navigation',
				'back'                        => 'Back',
				'home'                        => 'Home',
				'global_clinic'               => 'Global Clinic',
			),
			'ur-PK' => array(
				'worldwide_clinic'            => 'عالمی کلینک',
				'hero_title'                  => 'عالمی ہومیوپیتھک نگہداشت اور پیشہ ورانہ موجودگی — اعتماد کے ساتھ مربوط',
				'hero_body'                   => 'مریضوں کے لیے تصدیق شدہ ڈاکٹر تلاش کرنے اور اہل ڈاکٹروں کے لیے عالمی کلینک کی منظوری کا سفر شروع کرنے کا ایک واضح اور بااعتماد دروازہ۔',
				'transparent_process'         => 'شفاف طریقۂ کار',
				'how_title'                   => 'عالمی کلینک کا سفر کس طرح مکمل ہوتا ہے؟',
				'journeys_label'              => 'مریض اور ڈاکٹر کے مراحل',
				'for_patients'                => 'مریضوں کے لیے',
				'patient_step_1'              => 'تصدیق شدہ عوامی ڈاکٹر ڈائریکٹری میں تلاش کریں۔',
				'patient_step_2'              => 'اصل ڈاکٹر پروفائل اور کلینک کی معلومات کا جائزہ لیں۔',
				'patient_step_3'              => 'محفوظ رابطہ، محفوظ کرنے یا ملاقات کے افعال کے لیے سائن اِن کریں۔',
				'patient_step_4'              => 'دستیابی، رضامندی اور بکنگ کی حالت کے لیے اصل کلینک نظام استعمال کریں۔',
				'for_doctors'                 => 'ڈاکٹروں کے لیے',
				'doctor_step_1'               => 'اعلیٰ اعتماد والا اکاؤنٹ بنائیں اور پلیٹ فارم کے قواعد قبول کریں۔',
				'doctor_step_2'               => 'شناخت اور پیشہ ورانہ شواہد فائل 09 میں جمع کریں۔',
				'doctor_step_3'               => 'ضرورت کے مطابق جانچ، مزید معلومات یا اپیل کے مراحل مکمل کریں۔',
				'doctor_step_4'               => 'منظوری کے بعد اصل پروفائل اور کلینک نظام میں اپنی معلومات قائم کریں۔',
				'find_doctor'                 => 'عالمی ڈاکٹر تلاش کریں',
				'start_clinic'                => 'اپنا عالمی کلینک شروع کریں',
				'content_review_title'        => 'مواد زیرِ جائزہ ہے',
				'content_review_body'         => 'منظور شدہ نسخہ عارضی طور پر دستیاب نہیں۔ ہوم استعمال کریں یا بعد میں دوبارہ آئیں۔',
				'destination_unavailable'     => 'یہ منزل عارضی طور پر دستیاب نہیں۔ کسی درخواست، بکنگ یا منظوری کو مکمل تصور نہیں کیا گیا۔',
				'trust_policy_details'        => 'اعتماد اور پالیسی کی تفصیل',
				'faq_title'                   => 'اکثر پوچھے جانے والے سوالات',
				'owner_unavailable'           => 'اصل متعلقہ منزل دستیاب نہیں؛ کوئی کارروائی تخلیق نہیں ہوئی۔',
				'service_unavailable'         => 'سروس عارضی طور پر دستیاب نہیں',
				'degraded_body'               => 'مطلوبہ اصل منزل دستیاب یا ہم آہنگ نہیں۔ کوئی بکنگ، درخواست، تصدیق یا طبی کارروائی تخلیق نہیں ہوئی۔',
				'emergency_title'             => 'ہنگامی حالت کی حد',
				'emergency_body'              => 'یہ پلیٹ فارم ہنگامی سروس نہیں۔ فوری یا جان لیوا علامات میں فوراً اپنی مقامی ہنگامی طبی خدمت سے رابطہ کریں۔',
				'page_navigation'             => 'صفحہ کی رہنمائی',
				'back'                        => 'واپس',
				'home'                        => 'ہوم',
				'global_clinic'               => 'عالمی کلینک',
			),
			'ar-SA' => array(
				'worldwide_clinic'            => 'العيادة العالمية',
				'hero_title'                  => 'رعاية هوميوباثية عالمية وحضور مهني — مترابطان بالثقة',
				'hero_body'                   => 'مدخل واضح للمرضى الباحثين عن أطباء موثَّقين وللأطباء المؤهلين لبدء مسار اعتماد عيادتهم العالمية.',
				'transparent_process'         => 'مسار شفاف',
				'how_title'                   => 'كيف تسير رحلة العيادة العالمية؟',
				'journeys_label'              => 'مسارا المريض والطبيب',
				'for_patients'                => 'للمرضى',
				'patient_step_1'              => 'ابحث في دليل الأطباء العام الموثَّق.',
				'patient_step_2'              => 'راجع ملف الطبيب الأساسي ومعلومات العيادة.',
				'patient_step_3'              => 'سجّل الدخول لإجراءات الاتصال المحمية والحفظ والمواعيد.',
				'patient_step_4'              => 'استخدم نظام العيادة الأصلي لمعرفة التوفر والموافقة وحالة الحجز.',
				'for_doctors'                 => 'للأطباء',
				'doctor_step_1'               => 'أنشئ حساباً عالي الثقة واقبل قواعد المنصة.',
				'doctor_step_2'               => 'قدّم إثباتات الهوية والمهنة إلى الملف 09.',
				'doctor_step_3'               => 'أكمل المراجعة أو طلب المعلومات الإضافية أو الاستئناف عند الحاجة.',
				'doctor_step_4'               => 'بعد الموافقة اضبط ملفك الأساسي وبيانات العيادة لدى مالكيها الأصليين.',
				'find_doctor'                 => 'ابحث عن طبيب عالمي',
				'start_clinic'                => 'ابدأ عيادتك العالمية',
				'content_review_title'        => 'المحتوى قيد المراجعة',
				'content_review_body'         => 'النسخة المعتمدة غير متاحة مؤقتاً. استخدم الصفحة الرئيسية أو عُد لاحقاً.',
				'destination_unavailable'     => 'هذه الوجهة غير متاحة مؤقتاً. لم يُفترض إنشاء طلب أو حجز أو موافقة.',
				'trust_policy_details'        => 'تفاصيل الثقة والسياسة',
				'faq_title'                   => 'الأسئلة الشائعة',
				'owner_unavailable'           => 'الوجهة الأصلية غير متاحة؛ لم يتم إنشاء أي إجراء.',
				'service_unavailable'         => 'الخدمة غير متاحة مؤقتاً',
				'degraded_body'               => 'الوجهة الأصلية المطلوبة غير متاحة أو غير متوافقة. لم يتم إنشاء حجز أو طلب أو تحقق أو إجراء سريري.',
				'emergency_title'             => 'حدود الطوارئ',
				'emergency_body'              => 'هذه المنصة ليست خدمة طوارئ. عند الأعراض العاجلة أو المهدِّدة للحياة اتصل فوراً بخدمات الطوارئ المحلية.',
				'page_navigation'             => 'تنقل الصفحة',
				'back'                        => 'رجوع',
				'home'                        => 'الرئيسية',
				'global_clinic'               => 'العيادة العالمية',
			),
		);
	}
}
