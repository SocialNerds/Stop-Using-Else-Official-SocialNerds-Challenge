<?php

/**
 * Some namespace, some libraries, some annotations.
 */
class ShiftCenterRestResource extends ResourceBase {

  /**
   * Some code.
   */

  /**
   * Get if user has access to this shift center.
   *
   * @param integer $uid
   *   User id.
   * @param integer $id
   *   Shift center id.
   *
   * @return bool
   *   True if user has access.
   */
  private function getAccess($uid, $id) {
    $variable = 'shiftcenter_access';
    $variable .= '_' . (string) $uid;
    $variable .= '_' . (string) $id;

    $access = &drupal_static($variable);

    if (!isset($access)) {
      // Get from cache.
      if ($cache = $this->cache->get($variable)) {
        return $cache->data;
      }
	    
	    $access = FALSE;
	    $shift_access = $this->shiftAccess;
	    
	    if ($shift_access->getPositionSameGroup($uid, $id)) {
	  	  // Check if it is view and user has access to all shifts.
	  	  $permission_all = $shift_access->getActionPermission($uid, 'action_shifts_view_all') 
                       || $shift_access->getActionPermission($uid, 'action_shifts_edit_all')
	  	  
		    // Check if user has access to see shifts below them.
	  	  $permission_bellow = $shift_access->getActionPermission($uid, 'action_shifts_view_bellow') 
	  					            || $shift_access->getActionPermission($uid, 'action_shifts_edit_bellow');
        
	  	  $below = $shift_access->getPositionBelow($uid, $id);
	  	  
	  	  $access = $permission_all || ($permission_bellow && $below) ? TRUE : FALSE;
      }
    
	    $this->cache->set($variable, $access, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);
    }

    return $access;
  }
  
  /**
   * Some code.
   */
}
