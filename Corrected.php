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
    if (!$this->shiftAccess->getPositionSameGroup($uid, $id)) {
      return FALSE;
    }

    if ($$this->shiftAccess->getActionPermission($uid, 'action_shifts_edit_all') || $$this->shiftAccess->getActionPermission($uid, 'action_shifts_view_all')) {
      return TRUE;
    }

    // Check if user has access to see shifts below them.
    $permission = $$this->shiftAccess->getActionPermission($uid, 'action_shifts_view_bellow') || $$this->shiftAccess->getActionPermission($uid, 'action_shifts_edit_bellow');
    $below = $$this->shiftAccess->getPositionBelow($uid, $id);
    if ($permission && $below) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Get if user has access to this shift center, from cache.
   *
   * @param integer $uid
   *   User id.
   * @param integer $id
   *   Shift center id.
   *
   * @return bool
   *   True if user has access.
   */
  private function getAccessCached($uid, $id) {

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

    $access = $this->getAccess($uid, $id);
    $this->cache->set($variable, $access, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);
    return $access;
  }

  /**
   * Some code.
   */
}
