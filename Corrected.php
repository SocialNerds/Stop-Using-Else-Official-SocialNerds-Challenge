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
        $variable = $this->getVariableFrom($uid, $id);

        // Get from drupal static object
        $access = &drupal_static($variable);
        if (isset($access)) {
            return $access;
        }

        // Get from cache.
        $cache = $this->getCache($variable);
        if (isset($cache)) {
            return $cache;
        }

        $shift_access = $this->shiftAccess;

        if (!$shift_access->getPositionSameGroup($uid, $id)) {
            return $this->setCacheAndReturn($variable, FALSE);
        }

        // Check if it is view and user has access to all shifts.
        $isPermittedToEditAll = $shift_access->getActionPermission($uid, 'action_shifts_edit_all');
        $isPermittedToViewAll = $shift_access->getActionPermission($uid, 'action_shifts_view_all');

        if ($isPermittedToEditAll || $isPermittedToViewAll) {
            return $this->setCacheAndReturn($variable, TRUE);
        }

        // Check if user has access to see shifts below them.
        $isPermittedToEditBellow = $shift_access->getActionPermission($uid, 'action_shifts_edit_bellow');
        $isPermittedToViewBellow = $shift_access->getActionPermission($uid, 'action_shifts_view_bellow');
        $permission = $isPermittedToEditBellow || $isPermittedToViewBellow;

        $below = $shift_access->getPositionBelow($uid, $id);

        return $this->setCacheAndReturn($variable, ($permission && $below));
    }

    /**
     * @param $uid
     *   User id.
     * @param $id
     *   Shift center id.
     * @return string
     */
    private function getVariableFrom($uid, $id): string
    {
        $variable = 'shiftcenter_access';
        $variable .= '_' . (string)$uid;
        $variable .= '_' . (string)$id;

        return $variable;
    }

    /**
     * @param $variable
     * @param $access
     * @return bool
     */
    private function setCacheAndReturn($variable, $access): bool
    {
        $this->setCache($variable, $access);
        return $access;
    }

    /**
     * @param $variable
     * @param $access
     */
    private function setCache($variable, $access)
    {
        $this->cache->set($variable, $access, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);
    }

    /**
     * @param $variable
     * @return mixed
     */
    private function getCache($variable)
    {
        return $this->cache->get($variable);
    }

    /**
     * Some code.
     */
}