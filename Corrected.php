<?php

class ShiftCenterRestResource extends ResourceBase {

  private function getAccess($uid, $id) {
    $variable = 'shiftcenter_access';
    $variable .= '_' . (string) $uid;
    $variable .= '_' . (string) $id;
    $access = &drupal_static($variable);
    if (isset($access)) {
	return $access;
    }
	  
    if ($cache = $this->cache->get($variable)) {
        return $cache->data;
    } 
	  
    $shift_access = $this->shiftAccess;
    if (!$shift_access->getPositionSameGroup($uid, $id)) {
	return $this->duplicateExterminator(false,$variable);
    }

    if ($shift_access->getActionPermission($uid, 'action_shifts_edit_all') 
        || $shift_access->getActionPermission($uid, 'action_shifts_view_all') 
        || (($shift_access->getActionPermission($uid, 'action_shifts_view_bellow') 
	     || $shift_access->getActionPermission($uid, 'action_shifts_edit_bellow')) 
           && $shift_access->getPositionBelow($uid, $id))) {
            return $this->duplicateExterminator(true,$variable);
    } 
    return $this->duplicateExterminator(false,$variable);
  }
	
  private function duplicateExterminator($bool,$variable) {
        $this->cache->set($variable, $bool, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);
        return $bool;
  }
}

