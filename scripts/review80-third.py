#!/usr/bin/env python3
"""Third independent 80-pass exact-source review gate for File 14 v1.4.1.

Each check is one independent final-state property. A PASS is repository evidence
only; it is not staging, deployed-code, DB, migration or live evidence.
"""
from pathlib import Path
import re

ROOT=Path(__file__).resolve().parents[1]
P=ROOT/'14-global-clinic-usp-integration'
def read(path): return (ROOT/path).read_text(encoding='utf-8')
loader=read('14-global-clinic-usp-integration/global-clinic-usp-integration.php')
install=read('14-global-clinic-usp-integration/includes/class-gcu-install.php')
repo=read('14-global-clinic-usp-integration/includes/class-gcu-repository.php')
rest=read('14-global-clinic-usp-integration/includes/class-gcu-rest.php')
front=read('14-global-clinic-usp-integration/includes/class-gcu-frontend.php')
future=read('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
future_policy=read('14-global-clinic-usp-integration/includes/class-gcu-future-policy.php')
guards=read('14-global-clinic-usp-integration/includes/class-gcu-future-guards.php')
policy=read('14-global-clinic-usp-integration/includes/class-gcu-policy.php')
caps=read('14-global-clinic-usp-integration/includes/class-gcu-capabilities.php')
privacy=read('14-global-clinic-usp-integration/includes/class-gcu-privacy.php')
review=read('14-global-clinic-usp-integration/includes/class-gcu-review80-hardening.php')
obs=read('14-global-clinic-usp-integration/includes/class-gcu-observability.php')
contracts=read('14-global-clinic-usp-integration/includes/class-gcu-contracts.php')
hard=read('14-global-clinic-usp-integration/includes/class-gcu-hardening.php')
css=read('14-global-clinic-usp-integration/assets/css/global-clinic-usp-integration.css')
future_css=read('14-global-clinic-usp-integration/assets/css/gcu-future-intelligence.css')
readme=read('14-global-clinic-usp-integration/readme.txt')
root_readme=read('README.md')
status=read('STATUS.md')
release=read('docs/RELEASE-EVIDENCE.md')
ledger=read('docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md')
quality=read('scripts/quality.sh')
workflow=read('.github/workflows/file14-quality.yml')
build=read('scripts/build.py')
all_php='\n'.join(p.read_text(encoding='utf-8') for p in P.rglob('*.php'))
exp_priv=privacy[privacy.find('public function export_data('):privacy.find('public function capture_attribution(')]
pub_start=contracts.find('public function public_destination($key)')
pub_end=contracts.find('public function public_destination_health()',pub_start)
pub_dto=contracts[pub_start:pub_end]
checks=[
('01 exact software and governing-plan identity','Version: 1.4.1' in loader and 'SSH-F14-FUTURE-CTI-2026-v2.0' in loader),
('02 base/Future schema identities remain separated',"GCU_SCHEMA_VERSION', 10004" in loader and "GCU_FUTURE_SCHEMA_VERSION', 1" in loader),
('03 status uses durable exact-current-main truth','exact current `main` SHA' in status and 'docs/REVIEW-80-THIRD-LEDGER-v1.4.1.md' in status),
('04 release evidence includes third-review and post-merge exact-head policy','third independent eighty-pass' in release and 'fresh post-merge' in release),
('05 base runtime readiness exists','public static function ready_for_runtime()' in install),
('06 install/upgrade/recovery lock is a runtime gate','gcu_install_in_progress' in install and 'LOCK_OPTION' in install),
('07 mutation readiness inherits runtime readiness','ready_for_mutation(){return self::ready_for_runtime();}' in install),
('08 public blocks fail closed and revalidate','function blocks(' in rest and 'GCU_Install::ready_for_runtime()' in rest and 'public, no-cache, max-age=0, must-revalidate' in rest),
('09 public destinations fail closed on runtime truth','function destinations()' in rest and rest.count('GCU_Install::ready_for_runtime()')>=4),
('10 REST event-token issue/verification honors runtime truth','can_issue_event_token' in rest and 'verify_event_token' in rest and rest.count('GCU_Install::ready_for_runtime()')>=4),
('11 repository token issue/consume has mutation readiness','issue_event_token' in repo and 'consume_event_token' in repo and repo.count('ready_for_mutation()')>=5),
('12 frontend route actions honor runtime truth','route_actions()' in front and 'GCU_Install::ready_for_runtime()' in front),
('13 shortcodes/rendering honor runtime truth','shortcode_global_clinic' in front and front.count('GCU_Install::ready_for_runtime()')>=4),
('14 frontend governed routes force cache revalidation','send_headers' in front and 'public, no-cache, max-age=0, must-revalidate' in front),
('15 base background workers skip when runtime unready','process_outbox()' in obs and 'process_inbox()' in obs and 'lifecycle_cleanup()' in obs and obs.count('GCU_Install::ready_for_runtime()')>=3),
('16 Future runtime inherits base runtime','$base_ready = GCU_Install::ready_for_runtime();' in future),
('17 Future daily governance has runtime boundary','public static function daily_governance()' in future and 'self::verify_schema();' in future),
('18 Future hourly intelligence has runtime boundary','public static function hourly_intelligence()' in future and future.count('self::runtime_ready()')>=6),
('19 Future cleanup has runtime boundary',"return array( 'skipped' => 'runtime_unready' )" in future),
('20 Future admin/assets fail closed when unready','public static function admin_page()' in future and 'public static function enqueue_assets()' in future and future.count('self::runtime_ready()')>=6),
('21 Future report create/resolve paths have runtime boundary','public static function create_report' in future and 'public static function resolve_report_record' in future and future.count('self::runtime_ready()')>=6),
('22 governed public cache no longer uses stale-while-revalidate','stale-while-revalidate' not in rest and 'stale-while-revalidate' not in future and 'public, no-cache, max-age=0, must-revalidate' in future),
('23 privacy email-subject export/erase never reads operator guest cookies','$_COOKIE' not in exp_priv),
('24 active blocks enforce their own review horizon','b.review_due_at IS NULL OR b.review_due_at>UTC_TIMESTAMP()' in repo),
('25 active blocks fail closed on missing/stale linked claims','$all_claims=array()' in repo and '$valid=$this->public_claims($all_claims)' in repo),
('26 public claims enforce review_due_at at query time','review_due_at IS NULL OR review_due_at>UTC_TIMESTAMP()' in repo),
('27 founder-approved copy receives a new review due date',"$u['review_due_at']=gmdate" in repo),
('28 canonical governed claims are deterministic English source text',"'The platform charges 0% commission on approved clinic transactions.'" in policy and "__( 'The platform charges 0% commission on approved clinic transactions.'" not in policy),
('29 authorization filters cannot elevate native capability denial','if ( ! $allowed ) { return false; }' in caps),
('30 public report sensitive-data gate covers Urdu and Arabic','شناختی' in future and 'هوية' in future),
('31 FAQ aggregate sensitive-data gate covers Urdu and Arabic','شناختی' in review and 'هوية' in review),
('32 scenario notes are internal-only','gcu_future_scenario_note_private' in guards),
('33 Future copy-quality reports are in privacy export/erase','gcu-copy-quality-reports' in exp_priv and 'actor_hash=NULL' in exp_priv),
('34 Future privacy export is paginated','LIMIT 201 OFFSET %d' in exp_priv and '$report_more' in exp_priv),
('35 no correction artifact variable-variable syntax',"${'all_claims'}" not in repo),
('36 base schema verifier checks required columns','missing_columns' in install and 'SHOW COLUMNS FROM' in install and "'commands'=>array('command_key'" in install),
('37 Future schema verifier checks required columns','missing_columns' in future and 'SHOW COLUMNS FROM' in future and "'records' => array( 'record_type'" in future),
('38 controlled Future schema force-verification exists','ensure_schema( $force_verify = false )' in future and 'ensure_future_schema($force_verify=false)' in install and 'ensure_future_schema(true)' in install),
('39 rollback is serialized by install lock',"gcu_rollback_locked" in install and 'finally{self::release_lock();}' in install),
('40 rollback snapshot/restoration covers Future governance data',"$maps['future_records']" in install and "$maps['future_reports']" in install and 'GCU_Future_Intelligence::SCHEMA_OPTION' in install),
('41 deactivation clears Future cron hooks',"'gcu_future_daily_governance','gcu_future_hourly_intelligence'" in install),
('42 base daily governance contains structural schema drift',"daily_governance_check(){$schema=GCU_Install::verify_schema()" in obs and "update_option('gcu_enabled',0,false)" in obs),
('43 Future daily governance contains structural schema drift','update_option( self::SAFE_MODE_OPTION, 1, false )' in future),
('44 all File14 REST responses receive safe trace identifier','X-GCU-Trace-ID' in review),
('45 base privileged REST mutations are rate bounded',"mutation_rate('content')" in rest and "mutation_rate('placement')" in rest and "mutation_rate('experiment')" in rest and "mutation_rate('workflow')" in rest),
('46 Future public report REST is durable-idempotent',"run_idempotent_command( 'future_report'" in future and 'required_idempotency_key( $request )' in future),
('47 Future governed-record write is rate bounded and idempotent',"future-record-write" in future and "run_idempotent_command( 'future_record_write'" in future),
('48 Future claim revalidation is rate bounded and idempotent',"future-claim-revalidate" in future and "run_idempotent_command( 'future_claim_revalidate'" in future),
('49 public readiness endpoint has anti-abuse bound',"consume_rate_limit( 'future-readiness', 60 )" in future),
('50 audit persistence failure enters containment','audit_lock_failed' in repo and 'audit_write_failed' in repo and "update_option('gcu_enabled',0,false)" in repo),
('51 outbox encoding/write failure enters containment','outbox_payload_invalid' in repo and 'outbox_write_failed' in repo and repo.count("update_option('gcu_enabled',0,false)")>=4),
('52 public destination DTO remains allowlisted/minimized',all(x in pub_dto for x in ("'key'=>","'available'=>","'url'=>","'reason'=>")) and all(x not in pub_dto for x in ("'owner'=>","'contract'=>","'verified_at'=>"))),
('53 strict same-origin includes scheme host effective port',all(x in hard for x in ('home_scheme','target_scheme','effective_port','strict_same_origin_url'))),
('54 consumer cannot elevate canonical destination-owner readiness','may never elevate owner readiness' in contracts),
('55 measurement remains explicit-consent bound','analytics_consent' in privacy and 'measurement_allowed' in privacy),
('56 Global Privacy Control remains honored','HTTP_SEC_GPC' in privacy),
('57 Save-Data/reduced-data suppression remains','HTTP_SAVE_DATA' in privacy and 'low_bandwidth_requested' in privacy),
('58 sensitive routes remain excluded from measurement','is_sensitive_path' in privacy),
('59 attribution remains File14-acquisition bounded and signed/minimized','is_file14_acquisition_route' in privacy and 'attribution' in privacy.lower() and 'hash_hmac' in privacy),
('60 event tokens remain atomic single-use','consumed_at IS NULL' in repo and 'token_hash' in repo),
('61 database rate limiting remains atomic','ON DUPLICATE KEY UPDATE counter=counter+1' in repo),
('62 durable idempotent command state remains','run_idempotent_command' in repo and "status='complete'" in repo),
('63 durable outbox retry/dead-letter remains','dispatch_outbox' in repo and "'dead'" in repo),
('64 durable inbox/stale-lock recovery remains','process_inbox' in repo and 'INTERVAL 10 MINUTE' in repo),
('65 tamper-evident audit-chain verification remains','verify_audit_chain' in repo and 'previous_hash' in repo and 'row_hash' in repo),
('66 WordPress privacy exporter and eraser remain registered','wp_privacy_personal_data_exporters' in privacy and 'wp_privacy_personal_data_erasers' in privacy),
('67 quality analytics suppress exact small-cohort count',"'sample_count' => GCU_Future_Policy::cohort_allowed( $selected ) ? $selected : null" in future),
('68 anomaly analytics suppress exact small-cohort current/baseline counts',"'current_sample' => null" in future and "'baseline_sample' => null" in future),
('69 friction analysis retains per-stage small-cohort sanitization','sanitize_friction_payload' in review and 'suppressed_stages' in review),
('70 FAQ intelligence cannot auto-publish','gcu_future_faq_suggestion_cannot_publish' in guards),
('71 AI copy remains draft-only and cannot auto-publish','gcu_future_ai_draft_cannot_publish' in guards and "auto_publish' => false" in future),
('72 governed public records retain Founder approval','APPROVE_CLAIMS' in guards and 'gcu_future_founder_approval_required' in guards),
('73 experiment safety retains meaningful guardrails/profiling block/early stop','guardrails_valid' in review and 'sensitive_sampling' in future_policy and 'early_stop_guard' in future),
('74 zero commission/free tier/optional support parity retained','0% commission, one free tier and optional support must remain in parity.' in future_policy and 'parity_status' in future),
('75 EN/UR/AR locale and RTL/LTR support retained',all(x in read('14-global-clinic-usp-integration/includes/class-gcu-future-i18n.php') for x in ("'ur-PK'","'ar-SA'")) and 'rtl' in css.lower()),
('76 accessibility/reduced-motion/forced-colors controls retained','44px' in css+future_css and 'focus-visible' in css+future_css and 'prefers-reduced-motion' in css+future_css and 'forced-colors' in future_css),
('77 PHP 7.4 and 8.3 exact-head matrix retained',"php: ['7.4','8.3']" in workflow),
('78 deterministic package SHA/SBOM gate retained','Deterministic double-build mismatch' in build and 'file-sha256-sbom-v1' in build and 'Unsafe archive path' in build),
('79 quality suite integrates third review and temporary repair machinery is absent','third-review-regression-tests.php' in quality and 'review80-third.py' in quality and not (ROOT/'.github/workflows/file14-third-review-corrective-patch.yml').exists() and not any((ROOT/'scripts'/n).exists() for n in ('apply-file14-third-review-corrections.py','apply-file14-third-review-deep-corrections.py','apply-file14-third-review-final-corrections.py','apply-file14-third-review-containment.py'))),
('80 Live-First truth boundary remains explicit','Never infer staging or live state from this repository alone' in root_readme and 'No `Staging-Accepted`, `Live-Deployed` or `Operational` claim' in status),
]
assert len(checks)==80, f'Internal third review definition drift: {len(checks)}'
failed=[]
for i,(name,ok) in enumerate(checks,1):
    print(f"Third Review {i:02d}: {'PASS' if ok else 'FAIL'} — {name}")
    if not ok: failed.append((i,name))
if failed:
    print('\nFailed third-review gates:')
    for i,name in failed: print(f'- Third Review {i:02d}: {name}')
    raise SystemExit(1)
print('\nThird eighty-pass repository review: PASS — 80/80 final-state gates satisfied')
