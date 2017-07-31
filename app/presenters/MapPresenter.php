<?php


class MapPresenter extends BasePresenter {
    /**
     * for access control
     * @return array
     */
    protected function getRoles(): array {
        return [UserManager::ROLE_VERIFIED_USER];
    }
}