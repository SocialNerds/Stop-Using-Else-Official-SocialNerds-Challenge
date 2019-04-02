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

	//NOTE(JohnMir): First time writing php so bear with me
	private function getAccess($uid, $id) 
	{
		$variable = 'shiftcenter_access';
		$variable .= '_' . (string) $uid;
		$variable .= '_' . (string) $id;
		$access = &drupal_static($variable);

		if (isset($access)) return $access

		//NOTE(JohnMir):Since I don't like this part I am gonna put on my cool glasses and pretend
		//I didn't write this (⌐■_■).
		do
		{
			//Check cache first
			if(cacheAccess($access , $variable)) break;

			shiftAccess($access , $uid , $id); 

			$this->cache->set($variable, $access, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);

		}while(FALSE)


		return $access;
	}

	private function cacheAccess(&$access , &$variable)
	{
		// Get from cache.
		if ($cache = $this->cache->get($variable)) 
		{
			$access = $cache->data;
			return TRUE;
		}
		return FALSE;
	}

	private function shiftAccess(&$access , $uid , $id)
	{
		$shift_access = $this->shiftAccess;

		if ($shift_access->getPositionSameGroup($uid, $id)) 
		{
			if(checkAccessInView($access , $uid , $shift_access)) return;

			checkAccessShifts($access , $shift_access, $uid , $id);
			return;
		}
		$access = FALSE;
	}

	private function checkAccessInView(&$access ,$uid , &$shift_access)
	{
		// Check if it is view and user has access to all shifts.
		if ($shift_access->getActionPermission($uid, 'action_shifts_edit_all') || $shift_access->getActionPermission($uid, 'action_shifts_view_all')) 
		{
			$access = TRUE;
			return TRUE; //TODO(JohnMir): Can we do "return $access=TRUE;" perhaps here and under?
		}
		$access = FALSE;
		return FALSE;
	}

	private function checkAccessShifts(&$access , &$shift_access $uid, $id)
	{
		// Check if user has access to see shifts below them.
		$permission = $shift_access->getActionPermission($uid, 'action_shifts_view_bellow') || $shift_access->getActionPermission($uid, 'action_shifts_edit_bellow');
		$below = $shift_access->getPositionBelow($uid, $id);
		if ($permission && $below) 
		{
			$access = TRUE;
			return;
		}
		$access = FALSE;
	}

  /**
   * Some code.
   */
}
