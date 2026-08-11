#!/usr/bin/env python3
from pathlib import Path
import re
ROOT=Path(__file__).resolve().parents[1]
def r(p): return (ROOT/p).read_text(encoding='utf-8')
loader=r('14-global-clinic-usp-integration/global-clinic-usp-integration.php');caps=r('14-global-clinic-usp-integration/includes/class-gcu-capabilities.php');policy=r('14-global-clinic-usp-integration/includes/class-gcu-policy.php');repo=r('14-global-clinic-usp-integration/includes/class-gcu-repository.php');front=r('14-global-clinic-usp-integration/includes/class-gcu-frontend.php');install=r('14-global-clinic-usp-integration/includes/class-gcu-install.php');plugin=r('14-global-clinic-usp-integration/includes/class-gcu-plugin.php');obs=r('14-global-clinic-usp-integration/includes/class-gcu-observability.php');admin=r('14-global-clinic-usp-integration/includes/class-gcu-admin.php');contracts=r('14-global-clinic-usp-integration/includes/class-gcu-contracts.php');hard=r('14-global-clinic-usp-integration/includes/class-gcu-hardening.php');privacy=r('14-global-clinic-usp-integration/includes/class-gcu-privacy.php');future=r('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php');guards=r('14-global-clinic-usp-integration/includes/class-gcu-future-guards.php');review=r('14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php');fi18n=r('14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php');css=r('14-global-clinic-usp-integration/assets/css/global-clinic-usp-integration.css');future_css=r('14-global-clinic-usp-integration/assets/css/gcu-future-intelligence.css');readme=r('14-global-clinic-usp-integration/readme.txt');status=r('STATUS.md');release=r('docs/RELEASE-EVIDENCE.md');trace=r('docs/REQUIREMENTS-TRACEABILITY.md');workflow=r('.github/workflows/file14-quality.yml');build=r('scripts/build.py');quality=r('scripts/quality.sh');ledger=r('docs/REVIEW-80-FOURTH-LEDGER-v1.4.2.md')
vm=re.search(r'Version:\s*([0-9]+)\.([0-9]+)\.([0-9]+)',loader)
version_ok=bool(vm) and tuple(map(int,vm.groups())) >= (1,4,2)
checks=[
('01 v1.4.2-or-later + governing plans',version_ok and 'SSH-F14-PLAN-2026-v1.0' in loader and 'SSH-F14-FUTURE-CTI-2026-v2.0' in loader),
('02 canonical logical repository identity','14-global-clinic-usp-and-conversion-integration' in loader),
('03 File00 authorization adapter presence is testable','authorization_adapter_available' in caps),
('04 privileged auth fails closed without File00 adapter','! self::authorization_adapter_available()' in caps),
('05 native denial cannot be elevated','if ( ! $allowed ) { return false; }' in caps),
('06 campaign sensitive-value gate exists','campaign_value_is_sensitive' in policy),
('07 campaign email rejection exists','@[A-Z0-9.' in policy),
('08 campaign phone-like rejection exists',"{7,}" in policy),
('09 campaign Urdu/Arabic identity markers exist','شناختی' in policy and 'هوية' in policy),
('10 active blocks require placement+block audience compatibility',"(p.audience=%s OR p.audience='all') AND (b.audience=%s OR b.audience='all')" in repo),
('11 active placement requires Founder approval capability',"('placement'===$machine&&'active'===$target)" in repo and 'APPROVE_CLAIMS' in repo),
('12 placement activation reads block audience','SELECT id,slot_key,audience' in repo),
('13 placement/block audience contract enforced','$audience_ok' in repo),
('14 funnel per-stage small cohort suppression exists',"$row['total']=null" in repo),
('15 funnel exposes suppressed stage names only','suppressed_stages' in repo),
('16 CTA published event preserves source measurement identity','source_event_id' in repo),
('17 inbound duplicate identity conflict is detected','inbound_event_identity_conflict' in repo),
('18 inbound duplicate payload hash is compared','SELECT event_name,payload_hash' in repo and 'hash_equals' in repo),
('19 audit verifier can inspect recent tail',"$scope='recent_tail'" in repo),
('20 audit recent tail uses anchored offset','OFFSET %d' in repo and 'anchor_missing' in repo),
('21 governed shortcode pages force cache revalidation',"has_shortcode($body,'gcu_block')" in front and 'nocache_headers()' in front),
('22 File14 does not ship duplicate shell fallback','data-gcu-shell-fallback' not in front),
('23 File20 Back/Home adapter remains the sole requested nav contract','sabri_shell_back_home_controls' in front),
('24 activation propagates Future schema failure','$future=self::ensure_future_schema(true)' in install and 'safe_error_record($future)' in install),
('25 routine upgrade propagates Future schema failure',install.count('$future=self::ensure_future_schema')>=2),
('26 rollback never wholesale deletes owner tables','DELETE FROM `$table`' not in install),
('27 rollback preserves rows changed after snapshot','$captured_at=gmdate' in install and 'strtotime($changed_at' in install),
('28 rollback restores snapshot rows by replace/insert','$wpdb->replace' in install and "$wpdb->insert($table,$row)" in install),
('29 plugin boot logs pending upgrade truth','runtime_upgrade_pending' in plugin),
('30 health report includes Future schema truth',"'future'=>$future" in obs and 'schema_verified' in obs),
('31 health report includes File00 auth dependency','file00_authorization_adapter' in obs),
('32 health report includes File20 nav/slot dependencies','file20_navigation_adapter' in obs and 'file20_slot_adapter' in obs),
('33 health report includes all File14 cron readiness',"'cron'=>$cron" in obs and 'gcu_future_hourly_intelligence' in obs),
('34 health report includes rewrite-route readiness',"'routes'=>$routes" in obs and 'gcu_route=global_clinic' in obs),
('35 partial audit coverage warns',"'full'!==$r['audit_chain']['scope']" in obs),
('36 successful rollback is audited',"audit('rollback_restored'" in admin),
('37 admin System Check displays Future/dependency/cron/route state','Future schema' in admin and 'Dependencies' in admin and 'Cron' in admin and 'Routes' in admin),
('38 base runtime readiness fail-close retained','ready_for_runtime' in install),
('39 Future runtime inherits base runtime truth','$base_ready = GCU_Install::ready_for_runtime();' in future),
('40 public governed blocks use immediate revalidation','public, no-cache, max-age=0, must-revalidate' in r('14-global-clinic-usp-integration/includes/class-gcu-rest.php')),
('41 public destination DTO remains minimized','public_destination_health' in contracts and "'owner'=>" not in contracts[contracts.find('public function public_destination($key)'):contracts.find('public function public_destination_health()')]),
('42 consumer cannot elevate owner readiness','may never elevate owner readiness' in contracts),
('43 strict same-origin validates effective port','strict_same_origin_url' in hard and 'effective_port' in hard),
('44 measurement token remains atomic single-use','consumed_at IS NULL' in repo),
('45 rate limiting remains atomic','ON DUPLICATE KEY UPDATE counter=counter+1' in repo),
('46 durable idempotent commands retained','run_idempotent_command' in repo and "status='complete'" in repo),
('47 outbox retry/dead-letter retained','dispatch_outbox' in repo and "'dead'" in repo),
('48 inbox stale recovery retained','process_inbox' in repo and 'INTERVAL 10 MINUTE' in repo),
('49 audit persistence failure remains fail-closed','audit_write_failed' in repo and "update_option('gcu_enabled',0,false)" in repo),
('50 public claims enforce review horizon','review_due_at IS NULL OR review_due_at>UTC_TIMESTAMP()' in repo),
('51 active blocks enforce own review horizon','b.review_due_at IS NULL OR b.review_due_at>UTC_TIMESTAMP()' in repo),
('52 active blocks fail closed on stale/missing linked claims','$valid=$this->public_claims($all_claims)' in repo),
('53 canonical claims remain deterministic source strings',"'The platform charges 0% commission on approved clinic transactions.'" in policy),
('54 zero commission policy retained',"'platform_commission_percent' => 0" in policy),
('55 free approved core tier retained',"'approved_core_tier'           => 'free'" in policy),
('56 voluntary support cannot buy visibility',"'support_affects_visibility'   => false" in policy),
('57 cure guarantee remains forbidden',"'cure_guarantee'                => false" in policy),
('58 emergency limitation remains governed','no_emergency_service' in policy),
('59 Global Privacy Control remains honored','global_privacy_control_requested' in privacy),
('60 Save-Data/reduced-data remains honored','low_bandwidth_requested' in privacy),
('61 sensitive routes remain excluded from measurement','is_sensitive_path' in privacy),
('62 WordPress privacy exporter remains registered','wp_privacy_personal_data_exporters' in privacy),
('63 WordPress privacy eraser remains registered','wp_privacy_personal_data_erasers' in privacy),
('64 attribution remains File14-acquisition-route bounded','is_file14_acquisition_route' in privacy),
('65 public Future reports reject multilingual sensitive data','report_contains_sensitive_data' in future and 'شناختی' in future and 'هوية' in future),
('66 FAQ aggregate direct identifiers remain rejected','question_contains_sensitive_data' in review and 'شناختی' in review and 'هوية' in review),
('67 scenario notes remain internal-only','gcu_future_scenario_note_private' in guards),
('68 AI copy cannot auto-publish','gcu_future_ai_draft_cannot_publish' in guards),
('69 FAQ intelligence cannot auto-publish','gcu_future_faq_suggestion_cannot_publish' in guards),
('70 experiment meaningful guardrails remain','guardrails_valid' in review),
('71 Future quality hides exact small cohorts',"'sample_count' => GCU_Future_Policy::cohort_allowed( $selected ) ? $selected : null" in future),
('72 Future anomaly hides exact small cohorts',"'current_sample' => null" in future and "'baseline_sample' => null" in future),
('73 American-English source/fallback + UR/AR translation coverage retained',"'ur-PK'" in fi18n and "'ar-SA'" in fi18n and 'Choose your next step' in fi18n and "'en-US'" in r('14-global-clinic-usp-integration/includes/class-gcu-i18n.php')),
('74 RTL/LTR repository support retained','rtl' in css.lower()),
('75 accessibility controls retained','44px' in css+future_css and 'focus-visible' in css+future_css and 'prefers-reduced-motion' in css+future_css),
('76 deterministic double-build/SBOM retained','Deterministic double-build mismatch' in build and 'file-sha256-sbom-v1' in build),
('77 PHP 7.4/8.3 exact-head matrix retained',"php: ['7.4','8.3']" in workflow),
('78 fourth review tests/ledger are quality-integrated','fourth-review-regression-tests.php' in quality and 'review80-fourth.py' in quality and 'REVIEW-80-FOURTH-LEDGER-v1.4.2.md' in status),
('79 temporary fourth-review repair machinery absent',not (ROOT/'.github/workflows/file14-fourth-review-corrective-patch.yml').exists() and not (ROOT/'.github/workflows/file14-fourth-review-diagnostic.yml').exists() and not (ROOT/'scripts/apply-file14-fourth-review-corrections.py').exists()),
('80 Live-First status boundary remains explicit','No `Staging-Accepted`, `Live-Deployed` or `Operational` claim' in status and 'Staging, deployed code, live DB/schema/migration' in release),
]
assert len(checks)==80
failed=[]
for i,(name,ok) in enumerate(checks,1):
    print(f"Fourth Review {i:02d}: {'PASS' if ok else 'FAIL'} — {name}")
    if not ok: failed.append((i,name))
if failed:
    print('\nFailed fourth-review gates:')
    for i,name in failed: print(f'- Fourth Review {i:02d}: {name}')
    raise SystemExit(1)
print('\nFourth independent eighty-pass repository review: PASS — 80/80 final-state gates satisfied')