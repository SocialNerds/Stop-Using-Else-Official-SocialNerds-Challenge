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

      if(
          $access ||
          $this->cache->get($variable)->data ||
          (
              $this->shiftAccess->getPositionSameGroup($uid, $id) &&
              (
                  $this->shiftAccess->getActionPermission($uid, 'action_shifts_edit_all') || $this->shiftAccess->getActionPermission($uid, 'action_shifts_view_all')
              )
          ) ||
          (
              $this->shiftAccess->getActionPermission($uid, 'action_shifts_view_bellow') || $this->shiftAccess->getActionPermission($uid, 'action_shifts_edit_bellow') &&
              $this->shiftAccess->getPositionBelow($uid, $id)
          )
      ){
        return true;
      }
      $this->cache->set($variable, false, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);
      return false;
  }

  /**
   * Some code.
   */
}
