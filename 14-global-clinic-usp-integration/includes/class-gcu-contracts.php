<?php

defined('ABSPATH')||exit;
final class GCU_Contracts{
// Consumer filters may never elevate owner readiness; they may only restrict a recently owner-confirmed state.
const STATE_PREFIX='gcu_destination_state_',REVALIDATION_OPTION='gcu_policy_revalidation_required';
public function hooks(){add_action('DoctorDirectoryAvailable.v1',array($this,'consume_directory_event'),10,1);add_action('ClinicBookingAvailable.v1',array($this,'consume_clinic_event'),10,1);add_action('DoctorOnboardingAvailable.v1',array($this,'consume_onboarding_event'),10,1);add_action('BusinessPolicyChanged.v1',array($this,'consume_business_policy_event'),10,1);}
public function destination_registry(){return array('doctor_directory'=>array('owner'=>'File 07','contract'=>'DoctorDirectoryAvailable.v1','fallback_url'=>home_url('/doctors/')),'clinic'=>array('owner'=>'File 08','contract'=>'ClinicBookingAvailable.v1','fallback_url'=>home_url('/worldwide-clinic/')),'doctor_onboarding'=>array('owner'=>'File 09','contract'=>'DoctorOnboardingAvailable.v1','fallback_url'=>home_url('/doctor/apply/')),'how_it_works'=>array('owner'=>'File 14','contract'=>'local','fallback_url'=>home_url('/clinic/how-it-works/')));}
public function destination($key){
$key=sanitize_key($key);$reg=$this->destination_registry();if(!isset($reg[$key])){return array('key'=>$key,'owner'=>'','available'=>false,'url'=>'','reason'=>'unknown_destination','contract'=>'');}
$def=$reg[$key];
if('File 14'===$def['owner']){$ready=GCU_Install::ready_for_runtime();$available=!is_wp_error($ready);return array('key'=>$key,'owner'=>$def['owner'],'available'=>$available,'url'=>$available?GCU_Hardening::strict_same_origin_url($def['fallback_url']):'','reason'=>$available?'ready':(is_wp_error($ready)?sanitize_key($ready->get_error_code()):'module_unready'),'contract'=>$def['contract'],'verified_at'=>time(),'source'=>'local');}
$base=null;
if(class_exists('GCU_Companion_Adapters')){$probe=GCU_Companion_Adapters::destination_probe($key);if(is_array($probe)){$base=$probe;}}
// R66: A cached event is historical evidence, never a live owner health grant.
// A negative or missing File07/08/09 native probe must not be overridden.
if(!is_array($base)){$base=array('key'=>$key,'owner'=>$def['owner'],'available'=>false,'url'=>'','reason'=>'owner_unconfirmed','contract'=>$def['contract'],'verified_at'=>0,'source'=>'none');}
$available=!empty($base['available']);$url=$available&&!empty($base['url'])?GCU_Hardening::strict_same_origin_url($base['url']):'';
if(!$url){$available=false;$url='';}
$filtered=apply_filters('gcu_destination_contract_v1',array('available'=>$available,'url'=>$url,'reason'=>$available?'owner_confirmed':sanitize_key(isset($base['reason'])?$base['reason']:'owner_unconfirmed')),$key,$def);
if(is_array($filtered)&&array_key_exists('available',$filtered)&&false===(bool)$filtered['available']){$available=false;$url='';}
// R66: filter consumers may restrict an owner-approved URL, not swap the destination.
if($available&&is_array($filtered)&&array_key_exists('url',$filtered)){
    $candidate=is_string($filtered['url'])?GCU_Hardening::strict_same_origin_url($filtered['url']):'';
    if(''===$candidate||$candidate!==$url){$available=false;$url='';}
}
return array('key'=>$key,'owner'=>$def['owner'],'available'=>$available,'url'=>$available?$url:'','reason'=>$available?'owner_confirmed':sanitize_key(isset($base['reason'])?$base['reason']:'owner_unconfirmed'),'contract'=>isset($base['contract'])?(string)$base['contract']:$def['contract'],'verified_at'=>isset($base['verified_at'])?(int)$base['verified_at']:0,'source'=>isset($base['source'])?sanitize_key($base['source']):'unknown');
}
public function all_destination_health(){$out=array();foreach(array_keys($this->destination_registry())as$key){$out[$key]=$this->destination($key);}return$out;}
public function public_destination($key){$d=$this->destination($key);return array('key'=>isset($d['key'])?sanitize_key($d['key']):sanitize_key($key),'available'=>!empty($d['available']),'url'=>!empty($d['url'])?GCU_Hardening::strict_same_origin_url($d['url']):'','reason'=>isset($d['reason'])?sanitize_key($d['reason']):'unavailable');}
public function public_destination_health(){$out=array();foreach(array_keys($this->destination_registry())as$key){$out[$key]=$this->public_destination($key);}return$out;}
public function placement_ready(array$p){
$slot=sanitize_key(isset($p['slot_key'])?$p['slot_key']:'');$route=sanitize_key(isset($p['route_key'])?$p['route_key']:'');
if('global_clinic'!==$route||!in_array($slot,array('global_clinic_primary','global_clinic_trust','global_clinic_steps','global_clinic_faq'),true)){return false;}
if(!class_exists('GCU_Companion_Adapters')||!GCU_Companion_Adapters::placement_contract_ready($route,$slot)){return false;}
$ready=apply_filters('gcu_file14_placement_ready_v1',true,$route,$slot,'File 14');
return true===$ready;
}
public function consume_directory_event($p){$this->consume_event('DoctorDirectoryAvailable.v1',$p);}public function consume_clinic_event($p){$this->consume_event('ClinicBookingAvailable.v1',$p);}public function consume_onboarding_event($p){$this->consume_event('DoctorOnboardingAvailable.v1',$p);}public function consume_business_policy_event($p){$this->consume_event('BusinessPolicyChanged.v1',$p);}
private function consume_event($name,$p){if(!is_array($p)){return false;}return GCU_Plugin::instance()->repository()->accept_inbound_event($name,$p);}
private function persist_option_exact($name,array$value){
    update_option($name,$value,false);
    $stored=get_option($name,null);
    // R64: boolean false from both failed JSON encoders hashes to the
    // same empty string, but is NOT evidence of persisted option equality.
    if(!is_array($stored)){return false;}
    $before=wp_json_encode($value);
    $after=wp_json_encode($stored);
    return is_string($before)&&is_string($after)
        && hash_equals(hash('sha256',$before),hash('sha256',$after));
}
public function apply_inbound_event($name,array$p){$map=array('DoctorDirectoryAvailable.v1'=>'doctor_directory','ClinicBookingAvailable.v1'=>'clinic','DoctorOnboardingAvailable.v1'=>'doctor_onboarding');if(isset($map[$name])){
    // R61: inbox rows may outlive the current producer validation logic.
    // A corrupt owner availability envelope must not become a trusted
    // File07/08/09 destination option via truthy coercion or absint().
    if(!isset($p['event_id'])||!is_string($p['event_id'])||!wp_is_uuid($p['event_id'])
       ||!array_key_exists('available',$p)||!is_bool($p['available'])){
        return new WP_Error('gcu_owner_event_envelope_unverified',__('Owner readiness envelope could not be verified.','global-clinic-usp-integration'));
    }
    // These inbox event names are explicitly .v1, not negotiated v2.
    // Never upgrade an unknown owner message's schema by integer casting.
    if(array_key_exists('contract_version',$p)
       && !in_array($p['contract_version'],array(1,'1'),true)){
        return new WP_Error('gcu_owner_event_contract_version_unverified',__('Owner event contract version was not verified.','global-clinic-usp-integration'));
    }
    $key=$map[$name];$url=!empty($p['url'])?GCU_Hardening::strict_same_origin_url($p['url']):'';if(!empty($p['available'])&&!$url){return new WP_Error('gcu_owner_event_url_invalid',__('Owner readiness cannot be accepted without a safe same-origin URL.','global-clinic-usp-integration'));}$r=array('available'=>!empty($p['available']),'url'=>$url,'reason'=>!empty($p['available'])?'owner_event_ready':'owner_event_unavailable','contract_version'=>isset($p['contract_version'])?(int)$p['contract_version']:1,'verified_at'=>time(),'event_id'=>sanitize_text_field($p['event_id']));if(!$this->persist_option_exact(self::STATE_PREFIX.$key,$r)){return new WP_Error('gcu_owner_state_persist_failed',__('Owner readiness could not be persisted safely.','global-clinic-usp-integration'));}return true;}if('BusinessPolicyChanged.v1'===$name){return$this->queue_policy_revalidation($p);}return new WP_Error('gcu_owner_event_unknown',__('Unknown owner event.','global-clinic-usp-integration'));}
private function queue_policy_revalidation(array$p){
    // R65: owner policy facts must have a verified *original* event ID,
    // schema and a meaningful source policy version, including on replay.
    if(!isset($p['event_id'])||!is_string($p['event_id'])||!wp_is_uuid($p['event_id'])){
        return new WP_Error('gcu_policy_event_invalid',__('Policy event identity is required.','global-clinic-usp-integration'));
    }
    if(array_key_exists('contract_version',$p)&&!in_array($p['contract_version'],array(1,'1'),true)){
        return new WP_Error('gcu_owner_event_contract_version_unverified',__('Owner event contract version was not verified.','global-clinic-usp-integration'));
    }
    if(!isset($p['policy_version'])||!is_string($p['policy_version'])
        ||''===trim($p['policy_version'])||strlen($p['policy_version'])>64){
        return new WP_Error('gcu_policy_version_unverified',__('Original owner policy version is required.','global-clinic-usp-integration'));
    }
    $version=GCU_Hardening::bounded_text(sanitize_text_field($p['policy_version']),64);
    if(''===trim($version)){
        return new WP_Error('gcu_policy_version_unverified',__('Original owner policy version is required.','global-clinic-usp-integration'));
    }
    if(array_key_exists('changed_keys',$p)){
        if(!is_array($p['changed_keys'])||count($p['changed_keys'])>50){
            return new WP_Error('gcu_policy_keys_unverified',__('Owner policy changed keys are invalid.','global-clinic-usp-integration'));
        }
        foreach($p['changed_keys'] as $key){
            if(!is_string($key)||''===sanitize_key($key)){
                return new WP_Error('gcu_policy_keys_unverified',__('Owner policy changed keys are invalid.','global-clinic-usp-integration'));
            }
        }
    }
    $keys=isset($p['changed_keys'])?array_values(array_unique(array_map('sanitize_key',$p['changed_keys']))):array();
    // Stable order permits genuine at-least-once replay without rewriting
    // the original evidence when the producer reorders identical key sets.
    sort($keys,SORT_STRING);
    $current=get_option(self::REVALIDATION_OPTION,array());
    $current=is_array($current)?$current:array();
    $id=$p['event_id'];
    if(array_key_exists($id,$current)){
        $old=$current[$id];
        $old_keys=is_array($old)&&isset($old['changed_keys'])&&is_array($old['changed_keys'])?$old['changed_keys']:null;
        if(null!==$old_keys){sort($old_keys,SORT_STRING);}
        if(is_array($old)&&isset($old['policy_version'])&&is_string($old['policy_version'])
           &&hash_equals($old['policy_version'],$version)&&$old_keys===$keys){
            // No second option mutation, received_at refresh or callback.
            return true;
        }
        return new WP_Error('gcu_policy_event_identity_conflict',__('Event ID was reused for different owner policy evidence.','global-clinic-usp-integration'));
    }
    $current[$id]=array('event_id'=>$id,'policy_version'=>$version,
        'changed_keys'=>$keys,'received_at'=>time());
    if(count($current)>100){
        uasort($current,static function($a,$b){return (int)$a['received_at']<=>(int)$b['received_at'];});
        $current=array_slice($current,-100,null,true);
    }
    if(!$this->persist_option_exact(self::REVALIDATION_OPTION,$current)){
        return new WP_Error('gcu_policy_revalidation_persist_failed',__('Policy revalidation state could not be persisted safely.','global-clinic-usp-integration'));
    }
    do_action('gcu_policy_revalidation_queued_v1',$current[$id]);
    return true;
}
}
