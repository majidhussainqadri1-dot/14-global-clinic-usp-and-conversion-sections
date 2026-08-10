<?php
$root=dirname(__DIR__);$p=$root.'/14-global-clinic-usp-integration';$f=array();function f4r($r){global$root;return file_get_contents($root.'/'.$r);}function f4c($c,$m){global$f;if(!$c){$f[]=$m;}}
$loader=f4r('14-global-clinic-usp-integration/global-clinic-usp-integration.php');$caps=f4r('14-global-clinic-usp-integration/includes/class-gcu-capabilities.php');$policy=f4r('14-global-clinic-usp-integration/includes/class-gcu-policy.php');$repo=f4r('14-global-clinic-usp-integration/includes/class-gcu-repository.php');$front=f4r('14-global-clinic-usp-integration/includes/class-gcu-frontend.php');$install=f4r('14-global-clinic-usp-integration/includes/class-gcu-install.php');$plugin=f4r('14-global-clinic-usp-integration/includes/class-gcu-plugin.php');$obs=f4r('14-global-clinic-usp-integration/includes/class-gcu-observability.php');$admin=f4r('14-global-clinic-usp-integration/includes/class-gcu-admin.php');$quality=f4r('scripts/quality.sh');$status=f4r('STATUS.md');$release=f4r('docs/RELEASE-EVIDENCE.md');
f4c(false!==strpos($loader,'Version: 1.4.2')&&false!==strpos($loader,"GCU_VERSION', '1.4.2"),'v1.4.2 release identity missing.');
f4c(false!==strpos($caps,'authorization_adapter_available')&&false!==strpos($caps,'! self::authorization_adapter_available()'),'File00 authorization dependency can fail open.');
f4c(false!==strpos($policy,'campaign_value_is_sensitive')&&false!==strpos($policy,"'شناختی'")&&false!==strpos($policy,"'هوية'"),'Campaign sensitive-data minimization missing.');
f4c(false!==strpos($repo,"(p.audience=%s OR p.audience='all') AND (b.audience=%s OR b.audience='all')"),'Block audience isolation missing.');
f4c(false!==strpos($repo,'APPROVE_CLAIMS')&&false!==strpos($repo,"'placement'")&&false!==strpos($repo,"'active'"),'Founder active-placement approval missing.');
f4c(false!==strpos($repo,'$audience_ok')&&false!==strpos($repo,'active block, audience contract'),'Placement/block audience compatibility missing.');
f4c(false!==strpos($repo,'suppressed_stages')&&false!==strpos($repo,'$safe[]=$row'),'Per-stage small-cohort suppression missing.');
f4c(false!==strpos($repo,'source_event_id'),'CTA source-event correlation missing.');
f4c(false!==strpos($repo,'inbound_event_identity_conflict')&&false!==strpos($repo,'SELECT event_name,payload_hash'),'Conflicting inbound event replay detection missing.');
f4c(false!==strpos($repo,'recent_tail')&&false!==strpos($repo,'OFFSET %d'),'Recent audit tail verification missing.');
f4c(false!==strpos($front,'has_shortcode')&&false!==strpos($front,'gcu_block')&&false!==strpos($front,'nocache_headers()'),'Shortcode cache freshness missing.');
f4c(false!==strpos($front,'sabri_shell_back_home_controls')&&false===strpos($front,'data-gcu-shell-fallback'),'File20-only navigation ownership regressed.');
f4c(substr_count($install,'$future=self::ensure_future_schema')>=2&&false!==strpos($install,'safe_error_record($future)'),'Future-schema error propagation missing.');
f4c(false===strpos($install,'DELETE FROM `$table`')&&false!==strpos($install,'captured_at')&&false!==strpos($install,'$wpdb->replace'),'Rollback can destructively delete post-snapshot data.');
f4c(false!==strpos($plugin,'runtime_upgrade_pending'),'Runtime upgrade error observability missing.');
f4c(false!==strpos($obs,'schema_verified')&&false!==strpos($obs,'dependencies')&&false!==strpos($obs,'cron')&&false!==strpos($obs,'routes'),'Expanded health evidence missing.');
f4c(false!==strpos($obs,'audit_chain')&&false!==strpos($obs,"'full'!=="),'Partial audit coverage does not warn.');
f4c(false!==strpos($admin,"audit('rollback_restored'")&&false!==strpos($admin,'Future schema')&&false!==strpos($admin,'Dependencies'),'Rollback/admin evidence missing.');
f4c(false!==strpos($quality,'fourth-review-regression-tests.php')&&false!==strpos($quality,'review80-fourth.py'),'Fourth review not integrated into quality.');
f4c(false!==strpos($status,'REVIEW-80-FOURTH-LEDGER-v1.4.2.md')&&false!==strpos($release,'REVIEW-80-FOURTH-LEDGER-v1.4.2.md'),'Fourth durable release evidence missing.');
if($f){fwrite(STDERR,"Fourth-review regression tests failed:\n- ".implode("\n- ",$f)."\n");exit(1);}echo"Fourth-review regression tests: PASS\n";
