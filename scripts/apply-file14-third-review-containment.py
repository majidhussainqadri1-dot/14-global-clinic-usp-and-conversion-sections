#!/usr/bin/env python3
from pathlib import Path
p=Path('14-global-clinic-usp-integration/includes/class-gcu-repository.php')
s=p.read_text(encoding='utf-8')
repls=[
("if(!$lock){GCU_Observability::log('error','audit_lock_failed',array('action'=>$action));return false;}","if(!$lock){update_option('gcu_enabled',0,false);GCU_Observability::log('error','audit_lock_failed',array('action'=>$action));return false;}"),
("if(false===$wpdb->insert($t['audit'],$r)){return false;}return$r['trace_id'];","if(false===$wpdb->insert($t['audit'],$r)){update_option('gcu_enabled',0,false);GCU_Observability::log('error','audit_write_failed',array('action'=>$action));return false;}return$r['trace_id'];"),
("if(false===$enc||strlen($enc)>20000){return false;}$ins=$wpdb->insert($t['outbox']","if(false===$enc||strlen($enc)>20000){update_option('gcu_enabled',0,false);GCU_Observability::log('error','outbox_payload_invalid',array('event'=>$name));return false;}$ins=$wpdb->insert($t['outbox']"),
("if(false===$ins){return false;}$this->dispatch_outbox($id,1);return$id;","if(false===$ins){update_option('gcu_enabled',0,false);GCU_Observability::log('error','outbox_write_failed',array('event'=>$name));return false;}$this->dispatch_outbox($id,1);return$id;")
]
changed=False
for old,new in repls:
    if new in s: continue
    if old not in s: raise SystemExit('Expected containment pattern missing: '+old[:100])
    s=s.replace(old,new,1);changed=True
p.write_text(s,encoding='utf-8')
print('audit/outbox containment:', 'applied' if changed else 'already applied')
