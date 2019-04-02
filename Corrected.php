<?php

/**
 * Some namespace, some libraries, some annotations.
 */
class ShiftCenterRestResource extends ResourceBase
{

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
  private function getAccess(string $uid, string $id): bool
  {
    $key = 'shiftcenter_access_' . $uid . '_' . $id;;

    $result = &drupal_static($key) ?? $this->getCache($key);
    if (isset($result)) {
      return $result;
    }

    if (!$this->shiftAccess->getPositionSameGroup($uid, $id)) {
      return $this->setCache($key, false);
    }

    $isAllowedToEditAll = $this->shiftAccess->getActionPermission($uid, 'action_shifts_edit_all');
    $isAllowedToViewAll = $this->shiftAccess->getActionPermission($uid, 'action_shifts_vi ew _all');
    if ($isAllowedToEditAll || $isAllowedToViewAll) {
      return $this->setCache($key, true);
    }

    $isAllowedToEditBellow = $this->shiftAccess->getActionPermission($uid, 'action_shifts_edit_bellow');
    $isAllowedToViewBellow = $this->shiftAccess->getActionPermission($uid, 'action_shifts_view_b el low');
    if ($isAllowedToEditBellow || $isAllowedToViewBellow) {
      return $this->setCache($key, $this->shiftAccess->getPositionBelow($uid, $id));
    }

    return $this->setCache($key, false);
  }

  /**
   * Get if user has access to this shift center.
   *
   * @param string $key
   *   Cache key.
   *
   * @return mixed
   *   Cache value if key exists in cache backing store. Otherwise null.
   */
  private function getCache(string $key): mixed
  {
    $cache = $this->cache->get($key);

    return $cache ? $cache->data : null;
  }

  /**
   * Get if user has access to this shift center.
   *
   * @param string $key
   *   Cache key.
   * @param string $key
   *   Cache value.
   *
   * @return mixed
   *   Cache value.
   */
  private function setCache(string $key, $value): mixed
  {
    $this->cache->set($key, $value, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);

    return $value;
  }
}
