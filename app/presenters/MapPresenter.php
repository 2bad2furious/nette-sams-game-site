<?php


class MapPresenter extends BasePresenter {
    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        $action = $this->getAction();
        if ($action === "show") {
            return UserManager::ROLES;
        }
        return [UserManager::ROLE_VERIFIED_USER];
    }
}