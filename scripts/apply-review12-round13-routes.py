from pathlib import Path
p=Path('14-global-clinic-usp-integration/includes/class-gcu-frontend.php')
s=p.read_text(encoding='utf-8')
old="private$current_route='',$degraded_destination='';private static$instance_counter=0;"
new="private$current_route='',$degraded_destination='';private$route_not_found=false;private static$instance_counter=0;"
if s.count(old)!=1: raise SystemExit(f'property anchor count={s.count(old)}')
s=s.replace(old,new,1)
old2="public function route_actions(){$this->current_route=sanitize_key((string)get_query_var('gcu_route'));if(!$this->current_route){return;}$ready=GCU_Install::ready_for_runtime();"
new2="public function route_actions(){$this->current_route=sanitize_key((string)get_query_var('gcu_route'));if(!$this->current_route){return;}if(!in_array($this->current_route,array('global_clinic','how_it_works','find_doctor','start_clinic'),true)){$this->route_not_found=true;status_header(404);return;}$ready=GCU_Install::ready_for_runtime();"
if s.count(old2)!=1: raise SystemExit(f'route anchor count={s.count(old2)}')
s=s.replace(old2,new2,1)
old3="public function head_meta(){$r=sanitize_key((string)get_query_var('gcu_route'));if(!$r){return;}if($this->degraded_destination){echo\"<meta name=\\\"robots\\\" content=\\\"noindex,nofollow\\\">\\n\";return;}$c="
new3="public function head_meta(){$r=sanitize_key((string)get_query_var('gcu_route'));if(!$r){return;}if($this->route_not_found||$this->degraded_destination){echo\"<meta name=\\\"robots\\\" content=\\\"noindex,nofollow\\\">\\n\";return;}$c="
if s.count(old3)!=1: raise SystemExit(f'head meta anchor count={s.count(old3)}')
s=s.replace(old3,new3,1)
old4="public function render_route(){$r=$this->current_route?$this->current_route:sanitize_key((string)get_query_var('gcu_route'));$ready=GCU_Install::ready_for_runtime();if($this->degraded_destination||is_wp_error($ready))"
new4="public function render_route(){$r=$this->current_route?$this->current_route:sanitize_key((string)get_query_var('gcu_route'));if($this->route_not_found){return$this->render_not_found();}$ready=GCU_Install::ready_for_runtime();if($this->degraded_destination||is_wp_error($ready))"
if s.count(old4)!=1: raise SystemExit(f'render route anchor count={s.count(old4)}')
s=s.replace(old4,new4,1)
anchor="private function render_degraded($dest){"
method="private function render_not_found(){$l=$this->current_locale();$root=$this->root_id('not-found');return$this->root_open($root,$l).$this->navigation_controls('not_found',$l).'<section class=\"gcu-state gcu-state--error\" role=\"status\"><h1>'.esc_html__('Page not found.','global-clinic-usp-integration').'</h1><p>'.esc_html__('The requested clinic route is not available.','global-clinic-usp-integration').'</p></section></div>';}\n"
if s.count(anchor)!=1: raise SystemExit(f'not-found method anchor count={s.count(anchor)}')
s=s.replace(anchor,method+anchor,1)
p.write_text(s,encoding='utf-8')

t=Path('tests/twelfth-cycle-round13-regression-tests.php')
t.write_text(r'''<?php
$root=dirname(__DIR__);$f=file_get_contents($root.'/14-global-clinic-usp-integration/includes/class-gcu-frontend.php');$failures=array();
function r12r13_assert($ok,$m){global $failures;if(!$ok){$failures[]=$m;}}
r12r13_assert(false!==strpos($f,"array('global_clinic','how_it_works','find_doctor','start_clinic')"),'Canonical route allowlist must be explicit.');
r12r13_assert(false!==strpos($f,'$this->route_not_found=true;status_header(404);'),'Unknown File 14 routes must be 404.');
r12r13_assert(false!==strpos($f,'$this->route_not_found||$this->degraded_destination'),'Unknown routes must be noindex and receive no canonical link.');
r12r13_assert(false!==strpos($f,'render_not_found()'),'Unknown routes must render a distinct not-found state.');
if($failures){fwrite(STDERR,"Twelfth-cycle Round 13 regression tests failed:\n- ".implode("\n- ",$failures)."\n");exit(1);}echo "Twelfth-cycle Round 13 regression tests: PASS\n";
''',encoding='utf-8')
q=Path('scripts/quality.sh');x=q.read_text(encoding='utf-8');a='php "$ROOT/tests/twelfth-cycle-round12-regression-tests.php"\n'
if x.count(a)!=1: raise SystemExit(f'quality anchor count={x.count(a)}')
q.write_text(x.replace(a,a+'php "$ROOT/tests/twelfth-cycle-round13-regression-tests.php"\n',1),encoding='utf-8')
print('Round 13 canonical route hardening applied.')