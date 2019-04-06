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
    private function getAccess($uid, $id)
    {
        $variable = 'shiftcenter_access';
        $variable .= '_' . (string)$uid;
        $variable .= '_' . (string)$id;

        $access = &drupal_static($variable);

        if (!isset($access)) {

            $access = $this->setBasedOnActionPermission($uid, $id, $variable);

            if ($cache = $this->cache->get($variable)) {
                $access = $cache->data;
            }
        }

        return $access;

    }

    private function setBasedOnActionPermission($uid, $id, $variable)
    {
        $shift_access = $this->shiftAccess;

        $position = $shift_access->getPositionSameGroup($uid, $id);

        $access = $position;

        if ($access) {
            $action_permission_edit = $shift_access->getActionPermission($uid, 'action_shifts_edit_all');
            $action_permission_view = $shift_access->getActionPermission($uid, 'action_shifts_view_all');

            $access = $action_permission_edit || $action_permission_view;
            if (!$access) {
                $action_permission_view_below = $shift_access->getActionPermission($uid, 'action_shifts_view_bellow');
                $action_permission_edit_below = $shift_access->getActionPermission($uid, 'action_shifts_edit_bellow');
                $permission = $action_permission_view_below || $action_permission_edit_below;

                $below  = $shift_access->getPositionBelow($uid, $id);
                $access = $permission && $below;
            }
        }

        $this->cache->set($variable, $access, $this->shiftTime->getTimestamp() + SHIFT_ACCESS_CACHE_TIME);

        return $access;

    }
}