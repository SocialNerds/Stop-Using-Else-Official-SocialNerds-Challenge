<?php

/**
 * Some namespace, some libraries, some annotations.
 */
class ShiftCenterRestResource extends ResourceBase
{

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
    private function getAccess($uid, $id)
    {
        $key = $this->accessKeyGenerator($uid, $uid);

        return $this->hasDrupalVariableStorageAccess($key) || $this->hasCachedAccess($key) ??
               $this->checkActionPermissions($uid, $uid);
    }

    /**
     * @param int $uid
     * @param int $id
     *
     * @return bool
     */
    private function checkActionPermissions(int $uid, int $id) : bool
    {
        $hasAccess = $this->shift_access->getPositionSameGroup($uid, $id) &&
                     ($this->hasAccessToAllShifts($uid) || $this->hasAccessToSeeShiftsBelow($uid, $id));

        $cacheTime = $this->shiftTime->getTimestamp() + self::SHIFT_ACCESS_CACHE_TIME;
        $accessKey = $this->accessKeyGenerator($uid, $id);
        $this->cache->set($accessKey, $hasAccess, $cacheTime);

        return $hasAccess;
    }

    /**
     * @param int $uid
     * @param int $id
     *
     * @return bool
     */
    private function hasAccessToSeeShiftsBelow(int $uid, int $id) : bool
    {
        return
            $this->shift_access->getPositionBelow($uid, $id) &&
            (
                $this->shift_access->getActionPermission($uid, 'action_shifts_view_bellow') ||
                $this->shift_access->getActionPermission($uid, 'action_shifts_edit_bellow')
            );
    }

    /**
     * @param int $uid
     *
     * @return bool
     */
    private function hasAccessToAllShifts(int $uid) : bool
    {
        return
            $this->shift_access->getActionPermission($uid, 'action_shifts_edit_all') ||
            $this->shift_access->getActionPermission($uid, 'action_shifts_view_all');
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    private function hasDrupalVariableStorageAccess(string $key) : bool
    {
        return &drupal_static($key) ?? false;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    private function hasCachedAccess(string $key) : bool
    {
        return null === $this->cache->get($key) ? false : $this->cache->get($key)->data;
    }

    /**
     * @param int $uid
     * @param int $id
     *
     * @return string
     */
    private function accessKeyGenerator(int $uid, int $id) : string
    {
        return sprintf('shiftcenter_access_%s_%s', $uid, $id);
    }

    /**
     * Some code.
     */
}
