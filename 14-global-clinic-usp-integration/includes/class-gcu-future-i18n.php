<?php

defined( 'ABSPATH' ) || exit;

/** Complete File14-owned public chrome translations for the Future CTI amendment. */
final class GCU_Future_I18n {
	public static function bootstrap() {
		add_filter( 'gcu_public_route_html', array( __CLASS__, 'localize_html' ), 20, 2 );
	}

	public static function strings( $locale ) {
		$locale = GCU_Policy::sanitize_locale( $locale );
		$sets = array(
			'ur-PK' => array(
				'Choose your next step' => 'اپنا اگلا مرحلہ منتخب کریں',
				'Choose explicitly. File 14 does not infer hidden health, identity or behavioral intent.' => 'اپنا مقصد خود واضح طور پر منتخب کریں۔ فائل 14 صحت، شناخت یا رویّے سے پوشیدہ مقصد اخذ نہیں کرتی۔',
				'Find a Global Doctor' => 'عالمی ڈاکٹر تلاش کریں',
				'Start Your Global Clinic' => 'اپنا عالمی کلینک شروع کریں',
				'Understand How It Works' => 'طریقۂ کار سمجھیں',
				'Search the canonical doctor directory and continue to the owner for availability or booking.' => 'اصل ڈاکٹر ڈائریکٹری میں تلاش کریں اور دستیابی یا وقت لینے کے لیے متعلقہ اصل نظام تک جائیں۔',
				'Review the requirements and continue to the File 09 onboarding owner.' => 'تقاضے دیکھیں اور فائل 09 کے اصل آن بورڈنگ نظام کی طرف جائیں۔',
				'Read the transparent patient and doctor journeys before taking an action.' => 'کسی عمل سے پہلے مریض اور ڈاکٹر کے واضح و شفاف مراحل پڑھیں۔',
				'Canonical destination is temporarily unavailable.' => 'متعلقہ اصل منزل عارضی طور پر دستیاب نہیں۔',
				'Understand the clinic journey' => 'کلینک کا طریقۂ سفر سمجھیں',
				'Return to Global Clinic' => 'عالمی کلینک پر واپس جائیں',
				'Review onboarding requirements' => 'آن بورڈنگ کے تقاضے دیکھیں',
				'Regional information' => 'علاقائی معلومات',
				'Trust evidence' => 'اعتماد کے شواہد',
				'Open a claim to see its basis, owner and review date.' => 'کسی دعوے کو کھول کر اس کی بنیاد، مالک اور نظرِ ثانی کی تاریخ دیکھیں۔',
				'Trust claims are temporarily unavailable because current evidence requires review.' => 'اعتماد کے دعوے عارضی طور پر دستیاب نہیں کیونکہ موجودہ شواہد کو نظرِ ثانی درکار ہے۔',
				'Basis' => 'بنیاد', 'Owner' => 'مالک', 'Effective' => 'نافذ از', 'Review due' => 'نظرِ ثانی کی مقررہ تاریخ', 'not set' => 'مقرر نہیں',
				'Choose a doctor safely' => 'ڈاکٹر کا محفوظ انتخاب کریں',
				'Check the doctor verification state and the source profile.' => 'ڈاکٹر کی تصدیقی حالت اور اصل پروفائل دیکھیں۔',
				'Review language, consultation mode, professional scope and any displayed fee before continuing.' => 'آگے بڑھنے سے پہلے زبان، مشاورت کا طریقہ، پیشہ ورانہ دائرہ اور ظاہر شدہ فیس دیکھیں۔',
				'Use only the canonical clinic or appointment owner for availability and booking.' => 'دستیابی اور وقت لینے کے لیے صرف اصل کلینک یا ملاقات کے نظام کو استعمال کریں۔',
				'Remember that verification is not a cure or outcome guarantee.' => 'یاد رکھیں کہ تصدیق شفا یا نتیجے کی ضمانت نہیں۔',
				'For urgent or life-threatening symptoms, seek immediate local emergency care.' => 'فوری یا جان لیوا علامات میں فوراً مقامی ہنگامی طبی مدد حاصل کریں۔',
				'Global Clinic readiness self-check' => 'عالمی کلینک تیاری کا خود جائزہ',
				'This is non-binding. Only File 09 / File 00 can determine verification or activation.' => 'یہ غیر حتمی خود جائزہ ہے۔ تصدیق یا فعالیت کا فیصلہ صرف فائل 09 / فائل 00 کے اصل نظام کرسکتے ہیں۔',
				'My account identity information is ready.' => 'میرے اکاؤنٹ کی شناختی معلومات تیار ہیں۔',
				'My professional evidence is ready for File 09 review.' => 'میرے پیشہ ورانہ شواہد فائل 09 کی جانچ کے لیے تیار ہیں۔',
				'My public professional profile information is ready.' => 'میرے عوامی پیشہ ورانہ پروفائل کی معلومات تیار ہیں۔',
				'My clinic information is ready.' => 'میرے کلینک کی معلومات تیار ہیں۔',
				'I have listed the languages in which I can consult.' => 'میں نے مشاورت کی زبانیں درج کردی ہیں۔',
				'I have defined online/in-person consultation modes.' => 'میں نے آن لائن/بالمشافہ مشاورت کے طریقے متعین کردیے ہیں۔',
				'I understand the privacy and public/private information boundaries.' => 'میں رازداری اور عوامی/نجی معلومات کی حدود سمجھتا ہوں۔',
				'I am ready to accept the platform rules and verification process.' => 'میں پلیٹ فارم کے قواعد اور تصدیقی طریقۂ کار قبول کرنے کے لیے تیار ہوں۔',
				'Check readiness' => 'تیاری جانچیں',
				'Complete the checklist to estimate preparation only.' => 'صرف تیاری کا اندازہ لگانے کے لیے فہرست مکمل کریں۔',
				'Clinic trust and policy change log' => 'کلینک اعتماد اور پالیسی تبدیلیوں کا ریکارڈ',
				'Future Conversion & Trust Intelligence v2.0' => 'مستقبل تبدیلی و اعتماد ذہانت v2.0',
				'Twenty-four Founder-approved ethical conversion, trust, privacy, experiment and transparency enhancements were added to File 14.' => 'بانی کی منظور کردہ اخلاقی تبدیلی، اعتماد، رازداری، تجربات اور شفافیت کی 24 جدید سہولیات فائل 14 میں شامل کی گئیں۔',
				'Report unclear, outdated or misleading clinic information' => 'غیر واضح، پرانی یا گمراہ کن کلینک معلومات کی اطلاع دیں',
				'Do not include personal, contact, identity or clinical details.' => 'ذاتی، رابطہ، شناختی یا طبی تفصیلات شامل نہ کریں۔',
				'Reason' => 'وجہ', 'Outdated' => 'پرانی', 'Misleading' => 'گمراہ کن', 'Unclear' => 'غیر واضح', 'Translation' => 'ترجمہ', 'Broken destination' => 'خراب منزل', 'Missing FAQ' => 'غائب عمومی سوال', 'Other' => 'دیگر',
				'Short explanation' => 'مختصر وضاحت', 'Send report' => 'اطلاع بھیجیں',
				'Preparation estimate:' => 'تیاری کا تخمینہ:',
				'This is not verification or approval; File 09 / File 00 remain the authoritative owners.' => 'یہ تصدیق یا منظوری نہیں؛ فائل 09 / فائل 00 ہی اصل حاکم نظام ہیں۔',
			),
			'ar-SA' => array(
				'Choose your next step' => 'اختر خطوتك التالية',
				'Choose explicitly. File 14 does not infer hidden health, identity or behavioral intent.' => 'اختر مقصدك صراحةً؛ لا تستنتج File 14 نية خفية من الصحة أو الهوية أو السلوك.',
				'Find a Global Doctor' => 'ابحث عن طبيب عالمي',
				'Start Your Global Clinic' => 'ابدأ عيادتك العالمية',
				'Understand How It Works' => 'افهم كيفية العمل',
				'Search the canonical doctor directory and continue to the owner for availability or booking.' => 'ابحث في دليل الأطباء الأصلي ثم انتقل إلى المالك المختص لمعرفة التوفر أو الحجز.',
				'Review the requirements and continue to the File 09 onboarding owner.' => 'راجع المتطلبات ثم انتقل إلى مالك مسار الانضمام في File 09.',
				'Read the transparent patient and doctor journeys before taking an action.' => 'اقرأ مساري المريض والطبيب بوضوح قبل اتخاذ أي إجراء.',
				'Canonical destination is temporarily unavailable.' => 'الوجهة الأصلية غير متاحة مؤقتاً.',
				'Understand the clinic journey' => 'افهم رحلة العيادة',
				'Return to Global Clinic' => 'العودة إلى العيادة العالمية',
				'Review onboarding requirements' => 'راجع متطلبات الانضمام',
				'Regional information' => 'معلومات إقليمية',
				'Trust evidence' => 'أدلة الثقة',
				'Open a claim to see its basis, owner and review date.' => 'افتح الادعاء لرؤية أساسه ومالكه وموعد مراجعته.',
				'Trust claims are temporarily unavailable because current evidence requires review.' => 'ادعاءات الثقة غير متاحة مؤقتاً لأن الأدلة الحالية تحتاج إلى مراجعة.',
				'Basis' => 'الأساس', 'Owner' => 'المالك', 'Effective' => 'نافذ من', 'Review due' => 'موعد المراجعة', 'not set' => 'غير محدد',
				'Choose a doctor safely' => 'اختر الطبيب بأمان',
				'Check the doctor verification state and the source profile.' => 'تحقق من حالة توثيق الطبيب والملف الأصلي.',
				'Review language, consultation mode, professional scope and any displayed fee before continuing.' => 'راجع اللغة وطريقة الاستشارة والنطاق المهني وأي رسوم ظاهرة قبل المتابعة.',
				'Use only the canonical clinic or appointment owner for availability and booking.' => 'استخدم فقط مالك العيادة أو المواعيد الأصلي لمعرفة التوفر والحجز.',
				'Remember that verification is not a cure or outcome guarantee.' => 'تذكر أن التحقق لا يضمن الشفاء أو النتيجة.',
				'For urgent or life-threatening symptoms, seek immediate local emergency care.' => 'للأعراض العاجلة أو المهددة للحياة اطلب الرعاية الطارئة المحلية فوراً.',
				'Global Clinic readiness self-check' => 'فحص ذاتي للاستعداد للعيادة العالمية',
				'This is non-binding. Only File 09 / File 00 can determine verification or activation.' => 'هذا فحص غير ملزم؛ لا يحدد التحقق أو التفعيل إلا المالكان الأصليان File 09 / File 00.',
				'My account identity information is ready.' => 'معلومات هوية حسابي جاهزة.',
				'My professional evidence is ready for File 09 review.' => 'أدلتي المهنية جاهزة لمراجعة File 09.',
				'My public professional profile information is ready.' => 'معلومات ملفي المهني العام جاهزة.',
				'My clinic information is ready.' => 'معلومات عيادتي جاهزة.',
				'I have listed the languages in which I can consult.' => 'حددت اللغات التي أستطيع الاستشارة بها.',
				'I have defined online/in-person consultation modes.' => 'حددت طرق الاستشارة عبر الإنترنت/حضوريًا.',
				'I understand the privacy and public/private information boundaries.' => 'أفهم حدود الخصوصية والمعلومات العامة/الخاصة.',
				'I am ready to accept the platform rules and verification process.' => 'أنا مستعد لقبول قواعد المنصة ومسار التحقق.',
				'Check readiness' => 'تحقق من الاستعداد',
				'Complete the checklist to estimate preparation only.' => 'أكمل القائمة لتقدير الاستعداد فقط.',
				'Clinic trust and policy change log' => 'سجل الثقة وتغييرات سياسة العيادة',
				'Future Conversion & Trust Intelligence v2.0' => 'ذكاء التحويل والثقة المستقبلي v2.0',
				'Twenty-four Founder-approved ethical conversion, trust, privacy, experiment and transparency enhancements were added to File 14.' => 'أضيفت إلى File 14 أربع وعشرون ميزة معتمدة من المؤسس للتحويل الأخلاقي والثقة والخصوصية والتجارب والشفافية.',
				'Report unclear, outdated or misleading clinic information' => 'أبلغ عن معلومات عيادة غير واضحة أو قديمة أو مضللة',
				'Do not include personal, contact, identity or clinical details.' => 'لا تدرج بيانات شخصية أو اتصال أو هوية أو تفاصيل سريرية.',
				'Reason' => 'السبب', 'Outdated' => 'قديمة', 'Misleading' => 'مضللة', 'Unclear' => 'غير واضحة', 'Translation' => 'الترجمة', 'Broken destination' => 'وجهة معطلة', 'Missing FAQ' => 'سؤال شائع مفقود', 'Other' => 'أخرى',
				'Short explanation' => 'شرح مختصر', 'Send report' => 'إرسال البلاغ',
				'Preparation estimate:' => 'تقدير الاستعداد:',
				'This is not verification or approval; File 09 / File 00 remain the authoritative owners.' => 'هذا ليس توثيقاً ولا موافقة؛ يبقى File 09 / File 00 المالكين المرجعيين.',
			),
		);
		return isset( $sets[ $locale ] ) ? $sets[ $locale ] : array();
	}

	public static function localize_html( $html, $route ) {
		if ( ! is_string( $html ) || false === strpos( $html, 'gcu-future' ) ) {
			return $html;
		}
		$locale = GCU_Plugin::instance()->frontend()->current_locale();
		$strings = self::strings( $locale );
		if ( ! $strings ) {
			return str_replace( 'data-gcu-readiness-form', 'data-gcu-readiness-form data-gcu-readiness-prefix="Preparation estimate:" data-gcu-readiness-disclaimer="This is not verification or approval; File 09 / File 00 remain the authoritative owners."', $html );
		}
		$replacements = array();
		foreach ( $strings as $english => $translated ) {
			$replacements[ esc_html( $english ) ] = esc_html( $translated );
			$replacements[ esc_attr( $english ) ] = esc_attr( $translated );
		}
		$html = strtr( $html, $replacements );
		$prefix = isset( $strings['Preparation estimate:'] ) ? $strings['Preparation estimate:'] : 'Preparation estimate:';
		$disclaimer = isset( $strings['This is not verification or approval; File 09 / File 00 remain the authoritative owners.'] ) ? $strings['This is not verification or approval; File 09 / File 00 remain the authoritative owners.'] : 'This is not verification or approval; File 09 / File 00 remain the authoritative owners.';
		return str_replace( 'data-gcu-readiness-form', 'data-gcu-readiness-form data-gcu-readiness-prefix="' . esc_attr( $prefix ) . '" data-gcu-readiness-disclaimer="' . esc_attr( $disclaimer ) . '"', $html );
	}
}
